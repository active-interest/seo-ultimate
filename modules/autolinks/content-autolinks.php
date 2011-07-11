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
	
	function init() {
		add_filter('the_content', array(&$this, 'autolink_content'));
		
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 35);
		add_filter('su_get_postmeta-autolinks', array(&$this, 'get_post_autolinks'), 10, 3);
		add_filter('su_custom_update_postmeta-autolinks', array(&$this, 'save_post_autolinks'), 10, 4);
		
		if ($this->is_action('update'))
			add_action('admin_footer', array(&$this, 'update_max_post_dates'));
		
		add_action('save_post', array(&$this, 'update_max_post_dates'));
		
		add_filter('su_get_setting-autolinks-linkfree_tags', array(&$this, 'filter_linkfree_tags'));
	}
	
	function filter_linkfree_tags($tags) {
		return sustr::preg_filter('a-z0-9,', strtolower($tags));
	}
	
	function autolink_content($content) {
		
		if ($this->get_postmeta('disable_autolinks')) return $content;
		
		$links = $this->get_setting('links', array());
		if (!count($links)) return $content;
		
		suarr::vklrsort($links, 'anchor');
		
		$count = 0; //Dummy var; needed for PHP4 compat
		$content = $this->_autolink_content($content, $links, $this->get_setting('limit_lpp_value', 5), $count);
		
		return $content;
	}
	
	function _autolink_content($content, $links, $limit, &$count, $round=1) {
		$limit_enabled = $this->get_setting('limit_lpp', false);
		if ($limit_enabled && $limit < 1) return $content;
		$oldlimit = $limit;
		
		$lpa_limit_enabled = $this->get_setting('limit_lpa', false);
		$lpa_limit = $lpa_limit_enabled ? $this->get_setting('limit_lpa_value', 5) : -1;
		
		$from_post_type = get_post_type();
		$dest_limit = $from_post_type ? (bool)$this->get_setting('dest_limit_' . $from_post_type, false) : false;
		$dest_limit_taxonomies = array();
		if ($dest_limit) {
			$from_post_type_taxonomies = suwp::get_object_taxonomy_names($from_post_type);
			foreach ($from_post_type_taxonomies as $from_post_type_taxonomy) {
				if ($this->get_setting('dest_limit_' . $from_post_type . '_within_' . $from_post_type_taxonomy, false))
					$dest_limit_taxonomies[] = $from_post_type_taxonomy;
			}
		}
		
		$post = get_post(suwp::get_post_id());
		
		foreach ($links as $data) {
			
			$anchor = $data['anchor'];
			$to_id = su_esc_attr($data['to_id']);
			
			if (strlen(trim($anchor)) && $to_id !== 0 && $to_id != 'http://') {
				
				if (isset($data['max_post_date']) && $data['max_post_date'] !== false && $post && sudate::gmt_to_unix($post->post_date_gmt) > sudate::gmt_to_unix($data['max_post_date']))
					continue;
				
				$type = $data['to_type'];
				
				if (sustr::startswith($type, 'posttype_')) {
					$to_id = intval($to_id);
					$to_post = get_post($to_id);
					
					if (get_post_status($to_id) != 'publish') continue;
					
					if (count($dest_limit_taxonomies)) {
						$shares_term = false;
						foreach ($dest_limit_taxonomies as $dest_limit_taxonomy) {
							$from_terms = suarr::flatten_values(get_the_terms(null, $dest_limit_taxonomy), 'term_id');
							
							if (is_object_in_taxonomy($to_post, $dest_limit_taxonomy))
								$to_terms = suarr::flatten_values(get_the_terms($to_id, $dest_limit_taxonomy), 'term_id');
							else
								$to_terms = array();
							
							if (count(array_intersect($from_terms, $to_terms))) {
								$shares_term = true;
								break;
							}
						}
						
						if (!$shares_term)
							continue;
					}
					
					$url = get_permalink($to_id);
				} elseif ($type == 'url')
					$url = $to_id;
				else
					$url = $this->jlsuggest_value_to_url($to_id ? "obj_$type/$to_id" : "obj_$type");
				
				if (!$this->get_setting('enable_self_links', false) && ($url == suurl::current() || $url == get_permalink()))
					continue;
				
				$rel	= $data['nofollow'] ? ' rel="nofollow"' : '';
				$target	= ($data['target'] == 'blank') ? ' target="_blank"' : '';
				$title	= strlen($titletext = su_esc_attr($data['title'])) ? " title=\"$titletext\"" : '';
				$a_url  = su_esc_attr($url);
				$h_anchor = esc_html($anchor);
				
				$link = "<a href=\"$a_url\"$title$rel$target>$1</a>";
				
				$content = sustr::htmlsafe_str_replace($h_anchor, $link, $content, $limit_enabled ? 1 : $lpa_limit, $new_count, $this->get_linkfree_tags());
				$count += $new_count;
				
				if ($limit_enabled) {
					$limit -= $new_count;
					if ($limit < 1) return $content;
				}
			}
		}
		
		if ($limit_enabled && $limit < $oldlimit && $round < $lpa_limit)
			$content = $this->_autolink_content($content, $links, $limit, $count, $round+1);
		
		return $content;
	}
	
	function get_linkfree_tags() {
		if ($linkfree_tags = $this->get_setting('linkfree_tags')) {
			$linkfree_tags = explode(',', $linkfree_tags);
			array_unshift($linkfree_tags, 'a');
		} else {
			$linkfree_tags = array('a');
		}
		
		return $linkfree_tags;
	}
	
	function update_max_post_dates() {
		if (!$this->get_setting('limit_sitewide_lpa', false))
			return;
		
		$sitewide_lpa = $this->get_setting('limit_sitewide_lpa_value', false);
		$lpp = $this->get_setting('limit_lpp_value', 5);
		
		global $wpdb;
		
		$links = $this->get_setting('links', array());
		$new_links = array();
		
		foreach ($links as $link_data) {
			
			$link_data['max_post_date'] = false;
			
			$posts_with_anchor = $wpdb->get_results( $wpdb->prepare(
				  "SELECT post_content, post_date_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND LOWER(post_content) LIKE %s ORDER BY post_date_gmt ASC"
				, '%' . like_escape(strtolower($link_data['anchor'])) . '%'
			));
			
			$count = 0;
			foreach ($posts_with_anchor as $post_with_anchor) {
				
				$new_count = 0;
				$this->_autolink_content($post_with_anchor->post_content, array($link_data), $lpp, $new_count);
				
				$count += $new_count;
				if ($count >= $sitewide_lpa) {
					$link_data['max_post_date'] = $post_with_anchor->post_date_gmt;
					break;
				}
			}
			
			$new_links[] = $link_data;
		}
		
		$this->update_setting('links', $new_links);
	}
	
	function admin_page_init() {
		$this->jlsuggest_init();
	}
	
	function admin_page_contents() {
		
		echo "\n<p>";
		_e('The Content Links section of Deeplink Juggernaut lets you automatically link a certain word or phrase in your post/page content to a URL you specify.', 'seo-ultimate');
		echo "</p>\n";
		
		$links = $this->get_setting('links', array());
		$num_links = count($links);
		
		if ($this->is_action('update')) {
			
			$links = array();
			
			$guid = stripslashes($_POST['_link_guid']);
			
			for ($i=0; $i <= $num_links; $i++) {
				
				$anchor = stripslashes($_POST["link_{$i}_anchor"]);
				
				$to	= stripslashes($_POST["link_{$i}_to"]);
				
				if (sustr::startswith($to, 'obj_')) {
					$to = sustr::ltrim_str($to, 'obj_');
					$to = explode('/', $to);
					if (count($to) == 2) {
						$to_type = $to[0];
						$to_id = $to[1];
					} else {
						$to_type = $to[0];
						$to_id = null;
					}
				} else {
					$to_type = 'url';
					$to_id = $to;
				}
				
				$title  = stripslashes($_POST["link_{$i}_title"]);
				
				$target = empty($_POST["link_{$i}_target"]) ? 'self' : 'blank';				
				
				$nofollow = isset($_POST["link_{$i}_nofollow"]) ? (intval($_POST["link_{$i}_nofollow"]) == 1) : false;
				$delete = isset($_POST["link_{$i}_delete"]) ? (intval($_POST["link_{$i}_delete"]) == 1) : false;
				
				if (!$delete && (strlen($anchor) || $to_id))
					$links[] = compact('anchor', 'to_type', 'to_id', 'title', 'nofollow', 'target');
			}
			$this->update_setting('links', $links);
			
			$num_links = count($links);
		}
		
		$guid = substr(md5(time()), 0, 10);
		
		if ($num_links > 0) {
			$this->admin_subheader(__('Edit Existing Links', 'seo-ultimate'));
			$this->content_links_form($guid, 0, $links);
		}
		
		$this->admin_subheader(__('Add a New Link', 'seo-ultimate'));
		$this->content_links_form($guid, $num_links, array(array()), false);
	}
	
	function content_links_form($guid, $start_id = 0, $links, $delete_option = true) {
		
		//Set headers
		$headers = array(
			  'link-anchor' => __('Anchor Text', 'seo-ultimate')
			, 'link-to' => __('Destination', 'seo-ultimate')
			, 'link-title' => __('Title Attribute', 'seo-ultimate')
			, 'link-options' => __('Options', 'seo-ultimate')
		);
		if ($delete_option) $headers['link-delete'] = __('Delete', 'seo-ultimate');
		
		//Begin table; output headers
		$this->admin_wftable_start($headers);
		
		//Cycle through links
		$i = $start_id;
		foreach ($links as $link) {
			
			if (!isset($link['anchor']))	$link['anchor'] = '';
			if (!isset($link['to_id']))		$link['to_id'] = '';
			if (!isset($link['to_type']))	$link['to_type'] = 'url';
			if (!isset($link['title']))		$link['title'] = '';
			if (!isset($link['nofollow']))	$link['nofollow'] = false;
			if (!isset($link['target']))	$link['target'] = '';
			
			$to_type_arr = array_pad(explode('_', $link['to_type'], 2), 2, null);
			$jlsuggest_box_params = array($to_type_arr[0], $to_type_arr[1], $link['to_id']);
			
			$cells = array(
				  'link-anchor' => $this->get_input_element('textbox', "link_{$i}_anchor", $link['anchor'])
				, 'link-to' => $this->get_jlsuggest_box("link_{$i}_to", $jlsuggest_box_params)
				, 'link-title' => $this->get_input_element('textbox', "link_{$i}_title", $link['title'])
				, 'link-options' =>
					 $this->get_input_element('checkbox', "link_{$i}_nofollow", $link['nofollow'], str_replace(' ', '&nbsp;', __('Nofollow', 'seo-ultimate')))
					.'<br />'
					.$this->get_input_element('checkbox', "link_{$i}_target", $link['target'] == 'blank', str_replace(' ', '&nbsp;', __('New window', 'seo-ultimate')))
			);
			if ($delete_option)
				$cells['link-delete'] = $this->get_input_element('checkbox', "link_{$i}_delete");
			
			$this->table_row($cells, $i, 'link');
			
			$i++;
		}
		
		$this->admin_wftable_end();
		echo $this->get_input_element('hidden', '_link_guid', $guid);
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
		$fields['38|disable_autolinks'] = $this->get_postmeta_checkbox('disable_autolinks', __('Don&#8217;t add autolinks to anchor texts found in this post.', 'seo-ultimate'), __('Autolink Exclusion:', 'seo-ultimate'));
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __('<strong>Incoming Autolink Anchors</strong> &mdash; When you enter anchors into this box, Deeplink Juggernaut will search for that anchor in all your other posts and link it to this post. For example, if the post you&#8217;re editing is about &#8220;blue widgets,&#8221; you could type &#8220;blue widgets&#8221; into the &#8220;Incoming Autolink Anchors&#8221; box and Deeplink Juggernaut will automatically build internal links to this post with that anchor text (assuming other posts contain that text).', 'seo-ultimate');
		return $help;
	}
}

}
?>