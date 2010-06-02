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
				
				$relnofollow = $data['nofollow'] ? ' rel="nofollow"' : '';
				
				//Special thanks to the GPL-licensed "SEO Smart Links" plugin for the following find/replace code
				//http://www.prelovac.com/vladimir/wordpress-plugins/seo-smart-links
				$replace = "<a title=\"$1\" href=\"$url\"$relnofollow>$1</a>";
				$reg = '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b($name)\b/imsU';
				$regexp = str_replace('$name', $anchor, $reg);
				$content = preg_replace($regexp, $replace, $content, $limit_enabled ? 1 : -1, $count);
				
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
		_e("The Deeplink Juggernaut can automatically link post/page anchor text to given URLs. This is a preview beta version. More functionality will be added in future releases of SEO Ultimate.", 'seo-ultimate');
		echo "</p>\n";
		
		if ($this->is_action('update')) {
			$links = array();
			for ($i=0; $i<20; $i++) {
				$anchor = stripslashes($_POST["link_{$i}_anchor"]);
				$url    = stripslashes($_POST["link_{$i}_url"]);
				$nofollow = intval($_POST["link_{$i}_nofollow"]) == 1;
				if (strlen($anchor) || strlen($url)) {
					$links[] = array(
						  'anchor' => $anchor
						, 'to_type' => 'url'
						, 'to_id' => $url
						, 'nofollow' => $nofollow
					);
				}
			}
			$this->update_setting('links', $links);
		} else {
			$links = $this->get_setting('links');
			if (!$links) $links = array();
		}
		
		$this->admin_wftable_start(array(
			  'link-anchor' => __('Anchor Text', 'seo-ultimate')
			, 'link-to_id' => __('URL', 'seo-ultimate')
			, 'link-nofollow' => __('Options', 'seo-ultimate')
		));
		
		for ($i=0; $i<20; $i++) {
			$anchor = su_esc_attr($links[$i]['anchor']);
			$url    = su_esc_attr($links[$i]['to_id']);
			echo "\t\t<tr>\n";
			echo "\t\t\t<td class='text'><input type='text' id='link_{$i}_anchor' name='link_{$i}_anchor' value='$anchor' /></td>\n";
			echo "\t\t\t<td class='text'><input type='text' class='text' id='link_{$i}_url' name='link_{$i}_url' value='$url' /></td>\n";
			echo "\t\t\t<td class='checkbox'><label for='link_{$i}_nofollow'><input type='checkbox' id='link_{$i}_nofollow' name='link_{$i}_nofollow' value='1'"; checked($links[$i]['nofollow']); echo " /> Nofollow</label></td>\n";
			echo "\t\t</tr>\n";
		}
		
		$this->admin_wftable_end();
	}
}

}
?>