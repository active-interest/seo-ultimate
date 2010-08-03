<?php
/**
 * Content Deeplink Juggernaut Module
 * 
 * @since 2.2
 */

if (class_exists('SU_Module')) {

class SU_ContentAutolinks extends SU_Module {
	
	function get_parent_module() { return 'autolinks'; }
	function get_child_order() { return 10; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Content Deeplink Juggernaut', 'seo-ultimate'); }
	function get_module_subtitle() { return __('Content Links', 'seo-ultimate'); }
	
	function get_default_settings() {
		return array(
			  'limit_lpp_value' => 5
			//, 'enable_self_links' => false
		);
	}
	
	function init() {
		add_filter('the_content', array(&$this, 'autolink_content'));
		
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 35);
		add_filter('su_get_postmeta-autolinks', array(&$this, 'get_post_autolinks'), 10, 3);
		add_filter('su_custom_update_postmeta-autolinks', array(&$this, 'save_post_autolinks'), 10, 4);
	}
	
	function autolink_content($content) {
		
		$links = $this->get_setting('links', array());
		if (!count($links)) return $content;
		
		suarr::vklrsort($links, 'anchor');
		
		$content = $this->_autolink_content($content, $links, $this->get_setting('limit_lpp_value', 5));
		
		return $content;
	}
	
	function _autolink_content($content, $links, $limit) {
		$limit_enabled = $this->get_setting('limit_lpp', false);
		if ($limit_enabled && $limit < 1) return $content;
		$oldlimit = $limit;
		
		foreach ($links as $data) {
			$anchor = $data['anchor'];
			$to_id = su_esc_attr($data['to_id']);
			
			if (strlen(trim($anchor)) && strlen(trim((string)$to_id)) && $to_id !== 0 && $to_id != 'http://') {
				
				$type = $data['to_type'];
				
				if ($type == 'url')
					$url = $to_id;
				elseif (($posttype = sustr::ltrim_str($type, 'posttype_')) != $type) {
					$to_id = (int)$to_id;
					if (get_post_status($to_id) != 'publish') continue;
					$url = get_permalink($to_id);
				} else
					continue;
				
				if (!$this->get_setting('enable_self_links', false) && $url == suurl::current())
					continue;
				
				$rel	= $data['nofollow'] ? ' rel="nofollow"' : '';
				$target	= ($data['target'] == 'blank') ? ' target="_blank"' : '';
				$title	= strlen($titletext = $data['title']) ? " title=\"$titletext\"" : '';
				
				$link = "<a href=\"$url\"$title$rel$target>$1</a>";
				
				$content = sustr::htmlsafe_str_replace($anchor, $link, $content, $limit_enabled ? 1 : -1, $count);
				
				if ($limit_enabled) {
					$limit -= $count;
					if ($limit < 1) return $content;
				}
			}
		}
		
		if ($limit_enabled && $limit < $oldlimit)
			$content = $this->_autolink_content($content, $links, $limit);
		
		return $content;
	}
	
	function admin_page_contents() {
		echo "\n<p>";
		_e('The Content Links section of Deeplink Juggernaut lets you automatically link a certain word or phrase in your post/page content to a URL you specify.', 'seo-ultimate');
		echo "</p>\n";
		
		$links = $this->get_setting('links', array());
		$num_links = count($links);
		
		if ($this->is_action('update')) {
			
			$links = array();
			
			for ($i=0; $i <= $num_links; $i++) {
				
				$guid   = stripslashes($_POST['_link_guid']);
				$anchor = stripslashes($_POST["link_{$i}_anchor"]);
				$to_type= stripslashes($_POST["link_{$i}_to_type__{$guid}"]);
				$to_id  = stripslashes($_POST["link_{$i}_to_id_{$to_type}__{$guid}"]);
				$title  = stripslashes($_POST["link_{$i}_title"]);
				
				$target = stripslashes($_POST["link_{$i}_target"]);
				if (!$target) $target = 'self';
				
				$nofollow = intval($_POST["link_{$i}_nofollow"]) == 1;
				$delete = intval($_POST["link_{$i}_delete"]) == 1;
				
				if (!$delete && (strlen($anchor) || $to_id))
					$links[] = compact('anchor', 'to_type', 'to_id', 'title', 'nofollow', 'target');
			}
			$this->update_setting('links', $links);
			
			$num_links = count($links);
		}
		
		if ($num_links > 0) {
			$this->admin_subheader(__('Edit Existing Links', 'seo-ultimate'));
			$this->content_links_form(0, $links);
		}
		
		$this->admin_subheader(__('Add a New Link', 'seo-ultimate'));
		$this->content_links_form($num_links, array(array()), false);
	}
	
	function content_links_form($start_id = 0, $links, $delete_option = true) {
		
		//Set headers
		$headers = array(
			  'link-anchor' => __('Anchor Text', 'seo-ultimate')
			, 'link-to_type' => __('Destination Type', 'seo-ultimate')
			, 'link-to_id' => __('Destination', 'seo-ultimate')
			, 'link-title' => __('Title Attribute', 'seo-ultimate')
			, 'link-options' => __('Options', 'seo-ultimate')
		);
		if ($delete_option) $headers['link-delete'] = __('Delete', 'seo-ultimate');
		
		//Begin table; output headers
		$this->admin_wftable_start($headers);
		
		//Get post options
		$posttypeobjs = suwp::get_post_type_objects();
		$posttypes = array();
		$posts = array();
		foreach ($posttypeobjs as $posttypeobj) {
			
			$stati = get_available_post_statuses($posttypeobj->name);
			suarr::remove_value($stati, 'auto-draft');
			$stati = implode(',', $stati);
			
			$typeposts = get_posts("post_status=$stati&numberposts=-1&post_type=".$posttypeobj->name);
			if (count($typeposts)) {
				$posttypes['posttype_'.$posttypeobj->name] = $posttypeobj->labels->singular_name;
				$posts['posttype_'.$posttypeobj->name] = suarr::simplify($typeposts, 'ID', 'post_title');
			}
		}
		
		//Cycle through links
		$i = $start_id;
		foreach ($links as $link) {
			
			$guid = substr(md5(time()), 0, 10);
			
			$postdropdowns = array();
			foreach ($posts as $posttype => $typeposts) {
				$typeposts = array(0 => '') + $typeposts; //Maintains numeric array keys, unlike array_unshift or array_merge
				$postdropdowns[$posttype] = $this->get_input_element('dropdown', "link_{$i}_to_id_{$posttype}__$guid", $link['to_id'], $typeposts);
			}
			
			$cells = array(
				  'link-anchor' => $this->get_input_element('hidden', '_link_guid', $guid)
								 . $this->get_input_element('textbox', "link_{$i}_anchor", $link['anchor'])
				, 'link-to_type' => $this->get_input_element('dropdown', "link_{$i}_to_type__$guid", $link['to_type'], array(
						  __('Custom', 'seo-ultimate') => array('url' => __('URL', 'seo-ultimate'))
						, __('Content Items', 'seo-ultimate') => $posttypes
					))
				, 'link-to_id' => $this->get_admin_form_subsections("link_{$i}_to_type__$guid", $link['to_type'] ? $link['to_type'] : 'url', array_merge(array(
						  'url' => $this->get_input_element('textbox', "link_{$i}_to_id_url", ($link['to_type'] == 'url') ? $link['to_id'] : '')
					), $postdropdowns))
				, 'link-title' => $this->get_input_element('textbox', "link_{$i}_title", $link['title'])
				, 'link-options' =>
					 $this->get_input_element('checkbox', "link_{$i}_nofollow", $link['nofollow'], __('Nofollow', 'seo-ultimate'))
					.$this->get_input_element('checkbox', "link_{$i}_target", $link['target'] != 'self', __('New window', 'seo-ultimate'))
			);
			if ($delete_option)
				$cells['link-delete'] = $this->get_input_element('checkbox', "link_{$i}_delete");
			
			$this->table_row($cells, $i, 'link');
			
			$i++;
		}
		
		$this->admin_wftable_end();
	}
	
	function get_post_autolinks($value, $key, $post) {
		$links = $this->get_setting('links', array());
		$postlinks = '';
		foreach ($links as $link_data) {
			if ($link_data['to_type'] == 'posttype_'.$post->post_type && $link_data['to_id'] == $post->ID)
				$postlinks .= $link_data['anchor']."\r\n";
		}
		return trim($postlinks);
	}
	
	function save_post_autolinks($false, $value, $metakey, $post) {
		if ($post->post_type == 'revision') return true;
		
		$links = $this->get_setting('links', array());
		$new_links = array();
		
		$keep_anchors = array();
		$others_anchors = array();
		$new_anchors = suarr::explode_lines($value);
		
		foreach ($links as $link_data) {
			if ($link_data['to_type'] == 'posttype_'.$post->post_type && $link_data['to_id'] == $post->ID) {
				if (in_array($link_data['anchor'], $new_anchors)) {
					$keep_anchors[] = $link_data['anchor'];
					$new_links[] = $link_data;
				}
			} else {
				$others_anchors[] = $link_data['anchor'];
				$new_links[] = $link_data;
			}
		}
		
		$anchors_to_add = array_diff($new_anchors, $keep_anchors, $others_anchors);
		
		foreach ($anchors_to_add as $anchor_to_add)
			$new_links[] = array(
				  'anchor' => $anchor_to_add
				, 'to_type' => 'posttype_'.$post->post_type
				, 'to_id' => $post->ID
				, 'title' => ''
				, 'nofollow' => false
				, 'target' => 'self'
			);
		
		$this->update_setting('links', $new_links);
		
		return true;
	}
	
	function postmeta_fields($fields) {
		$fields['35|autolinks'] = $this->get_postmeta_textarea('autolinks', __('Incoming Autolink Anchors:<br /><em>(one per line)</em>', 'seo-ultimate'));
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __('<strong>Incoming Autolink Anchors</strong> &mdash; When you enter anchors into this box, Deeplink Juggernaut will search for that anchor in all your other posts and link it to this post. For example, if the post you&#8217;re editing is about &#8220;blue widgets,&#8221; you could type &#8220;blue widgets&#8221; into the &#8220;Incoming Autolink Anchors&#8221; box and Deeplink Juggernaut will automatically build internal links to this post with that anchor text (assuming other posts contain that text).', 'seo-ultimate');
		return $help;
	}
}

}
?>