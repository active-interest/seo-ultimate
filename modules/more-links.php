<?php
/**
 * More Link Customizer Module
 * 
 * @version 1.0
 * @since 1.3
 */

if (class_exists('SU_Module')) {

class SU_MoreLinks extends SU_Module {
	
	function get_menu_title() { return __('More Link Customizer', 'seo-ultimate'); }
	
	function get_default_settings() {
		return array(
			  'default' => 'Continue reading &#8220;{post}&#8221; &raquo;'
		);
	}
	
	function init() {
		add_filter('the_content_more_link', array(&$this, 'more_link_filter'), 10, 2);
		add_filter('su_get_postmeta-morelinktext', array(&$this, 'get_morelinktext_postmeta'), 10, 3);
	}
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->textbox('default', __("Default More Link Text", 'seo-ultimate'), $this->get_default_setting('default'));
		$this->admin_form_end();
	}
	
	function more_link_filter($link, $text) {
		$default = $this->get_setting('default');
		
		if (strlen($newtext = trim($this->get_postmeta('morelinktext'))) || strlen(trim($newtext = $default))) {
			$newtext = str_replace('{post}', wp_specialchars(get_the_title()), $newtext);
			$link = str_replace("$text</a>", "$newtext</a>", $link);
		}
		
		return $link;
	}
	
	function postmeta_fields($fields, $screen) {
		
		if (strcmp($screen, 'post') == 0)
			$fields['40|morelinktext'] = $this->get_postmeta_textbox('morelinktext', __('More Link Text:', 'seo-ultimate'));
		
		return $fields;
	}
	
	function get_morelinktext_postmeta($value, $key, $post) {
		
		if (!strlen($value)) {
			
			//Import any custom anchors from the post itself
			$content = $post->post_content;
			$matches = array();
			if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
				$content = explode($matches[0], $content, 2);
				if ( !empty($matches[1]) )
					return strip_tags(wp_kses_no_null(trim($matches[1])));
			}
		}
		
		return $value;
	}
	
	function admin_help() {
		return __("
<ul>
	<li><p><strong>What it does:</strong> More Link Customizer lets you modify the anchor text of your posts&#8217; <a href='http://codex.wordpress.org/Customizing_the_Read_More' target='_blank'>&#8220;more&#8221; links</a>.</p></li>
	<li><p><strong>Why it helps:</strong> On the typical WordPress setup, the &#8220;more link&#8221; always has the same anchor text (e.g. &#8220;Read more of this entry &raquo;&#8221;). Since internal anchor text conveys web page topicality to search engines, the &#8220;read more&#8221; phrase isn&#8217;t a desirable anchor phrase. More Link Customizer lets you replace the boilerplate text with a new anchor that, by default, integrates your post titles (which will ideally be keyword-oriented).</p></li>
	<li><p><strong>How to use it:</strong> On this page you can set the anchor text you&#8217;d like to use by default. The <code>{post}</code> variable will be replaced with the post&#8217;s title. HTML and encoded entities are supported. If instead you decide that you&#8217;d like to use the default anchor text specified by your currently-active theme, just erase the contents of the textbox. The anchor text can be overriden on a per-post basis via the &#8220;More Link Text&#8221; box in the &#8220;SEO Settings&#8221; section of the WordPress post editor.</p></li>
</ul>
", 'seo-ultimate');
	}
}

}
?>