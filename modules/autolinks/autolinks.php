<?php
/**
 * Deeplink Juggernaut Module
 * 
 * @version 0.1
 * @since 1.8
 */

if (class_exists('SU_Module')) {

class SU_Autolinks extends SU_Module {
	
	function get_module_title() { return __('Deeplink Juggernaut', 'seo-ultimate'); }
	function get_page_title()   { return __('Deeplink Juggernaut (Beta)', 'seo-ultimate'); }
	
	function init() {
		add_filter('the_content', array(&$this, 'autolink_content'));
	}
	
	function autolink_content($content) {
		
		$links = $this->get_setting('links');
		if (!count($links)) return $content;
		
		suarr::vklrsort($links, 'anchor');
		
		foreach ($links as $data) {
			$anchor = $data['anchor'];
			$url = su_esc_attr($data['to_id']);
			$type = $data['to_type'];
			
			if ($type == 'url' && strlen(trim($anchor)) && strlen(trim($url))) {
				//Special thanks to the GPL-licensed "SEO Smart Links" plugin for the following find/replace code
				//http://www.prelovac.com/vladimir/wordpress-plugins/seo-smart-links
				$replace = "<a title=\"$1\" href=\"$url\">$1</a>";
				$reg = '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b($name)\b/imsU';
				$regexp = str_replace('$name', $anchor, $reg);
				$content = preg_replace($regexp, $replace, $content);
			}
		}
		
		return $content;
	}
	
	function admin_page_contents() {
		echo "\n<p>";
		_e("The Deeplink Juggernaut can automatically link post/page anchor text to given URLs. This is a preview beta version. More functionality will be added in future releases of SEO Ultimate.", 'seo-ultimate');
		echo "</p>\n";
		
		$this->admin_form_start(false, false);
		
		if ($this->is_action('update')) {
			$links = array();
			for ($i=0; $i<20; $i++) {
				$anchor = stripslashes($_POST["link_{$i}_anchor"]);
				$url    = stripslashes($_POST["link_{$i}_url"]);
				if (strlen($anchor) || strlen($url)) {
					$links[] = array('anchor' => $anchor, 'to_type' => 'url', 'to_id' => $url);
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
		));
		
		for ($i=0; $i<20; $i++) {
			$anchor = su_esc_attr($links[$i]['anchor']);
			$url    = su_esc_attr($links[$i]['to_id']);
			echo "\t\t<tr><td><input type='text' id='link_{$i}_anchor' name='link_{$i}_anchor' value='$anchor' /></td><td><input type='text' id='link_{$i}_url' name='link_{$i}_url' value='$url' /></td></tr>\n";
		}
		
		$this->admin_wftable_end();
		$this->admin_form_end(false, false);
	}
}

} elseif ($_GET['css'] == 'admin') {
	header('Content-type: text/css');
?>

#su-autolinks table.widefat,
#su-autolinks table.widefat input {
	width: 100%;
}

<?php
}
?>