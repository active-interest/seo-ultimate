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
		);
	}
	
	function init() {
		add_filter('the_content', array(&$this, 'autolink_content'));
	}
	
	function autolink_content($content) {
		
		$links = $this->get_setting('links');
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
			$url = su_esc_attr($data['to_id']);
			$type = $data['to_type'];
			
			if ($type == 'url' && strlen(trim($anchor)) && strlen(trim($url))) {
				
				$rel	= $data['nofollow'] ? ' rel="nofollow"' : '';
				$target	= ($data['target'] == 'blank') ? ' target="_blank"' : '';
				$title	= strlen($titletext = $data['title']) ? " title=\"$titletext\"" : '';
				
				$link = "<a title=\"$1\" href=\"$url\"$title$rel$target>$1</a>";
				
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
		
		$links = $this->get_setting('links');
		if (!is_array($links)) $links = array();
		$num_links = count($links);
		
		if ($this->is_action('update')) {
			
			$links = array();
			
			for ($i=0; $i <= $num_links; $i++) {
				
				$anchor = stripslashes($_POST["link_{$i}_anchor"]);
				$url    = stripslashes($_POST["link_{$i}_url"]);
				$title  = stripslashes($_POST["link_{$i}_title"]);
				
				$target = stripslashes($_POST["link_{$i}_target"]);
				if (!$target) $target = 'self';
				
				$nofollow = intval($_POST["link_{$i}_nofollow"]) == 1;
				$delete = intval($_POST["link_{$i}_delete"]) == 1;
				
				if (!$delete && (strlen($anchor) || strlen($url))) {
					$links[] = array(
						  'anchor' => $anchor
						, 'to_type' => 'url'
						, 'to_id' => $url
						, 'title' => $title
						, 'nofollow' => $nofollow
						, 'target' => $target
					);
				}
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
		
		$headers = array(
			  'link-anchor' => __('Anchor Text', 'seo-ultimate')
			, 'link-to_id' => __('URL', 'seo-ultimate')
			, 'link-title' => __('Title Attribute', 'seo-ultimate')
			, 'link-options' => __('Options', 'seo-ultimate')
		);
		
		if ($delete_option) $headers['link-delete'] = __('Delete', 'seo-ultimate');
		
		$this->admin_wftable_start($headers);
		
		$i = $start_id;
		foreach ($links as $link) {
			$anchor = su_esc_attr($link['anchor']);
			$url    = su_esc_attr($link['to_id']);
			$title  = su_esc_attr($link['title']);
			echo "\t\t<tr>\n";
			
			$fields = array('anchor', 'to_id' => 'url', 'title');
			foreach ($fields as $class => $field) {
				if (is_numeric($class)) $class = $field;
				echo "\t\t\t<td class='text su-link-$class'><input type='text' id='link_{$i}_$field' name='link_{$i}_$field' value='{$$field}' autocomplete='off' /></td>\n";
			}
			
			echo "\t\t\t<td class='checkbox su-link-options'>";
				echo "<label for='link_{$i}_nofollow'><input type='checkbox' id='link_{$i}_nofollow' name='link_{$i}_nofollow' value='1'"; checked($link['nofollow']); echo " /> Nofollow</label>";
				echo "<label for='link_{$i}_target'><input type='checkbox' id='link_{$i}_target' name='link_{$i}_target' value='blank'"; checked($link['target'] == 'blank'); echo " /> New window</label>";
			echo "</td>\n";
			
			if ($delete_option) echo "\t\t\t<td class='checkbox su-link-delete'><input type='checkbox' id='link_{$i}_delete' name='link_{$i}_delete' value='1' /></td>\n";
			/*echo "\t\t\t<td class='dropdown'><select id='link_{$i}_target' name='link_{$i}_target'>";
			echo suhtml::option_tags(array(
				  'self' => __('Default', 'seo-ultimate')
				, 'blank' => __('_blank (New window)', 'seo-ultimate')
				, 'top' => __('_top (Same window, no frames)', 'seo-ultimate')
				, 'parent' => __('_parent (Same window, parent frameset)', 'seo-ultimate')
			));
			echo "</select></td>\n";*/
			echo "\t\t</tr>\n";
			$i++;
		}
		
		$this->admin_wftable_end();
	}
}

}
?>