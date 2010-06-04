<?php
/**
 * Meta Editor Module
 * 
 * @since 0.3
 */

if (class_exists('SU_Module')) {

class SU_Meta extends SU_Module {
	
	function get_module_title() { return __('Meta Editor', 'seo-ultimate'); }
	
	function init() {
		add_filter('su_meta_robots', array(&$this, 'meta_robots'));
		add_action('su_head', array(&$this, 'head_tag_output'));
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 20);
	}
	
	//Add the appropriate commands to the meta robots array
	function meta_robots($commands) {
		
		$tags = array('noodp', 'noydir', 'noarchive');
		
		foreach ($tags as $tag) {
			if ($this->get_setting($tag)) $commands[] = $tag;
		}
		
		return $commands;
	}
	
	function head_tag_output() {
		
		$desc = false;
		$kw = false;
		
		//If we're viewing the homepage, look for homepage meta data.
		if (is_home()) {
			$desc = $this->get_setting('home_description');
			if (!$desc && $this->get_setting('home_description_tagline_default')) $desc = get_bloginfo('description');
			$kw = $this->get_setting('home_keywords');
		
		//If we're viewing a post or page, look for its meta data.
		} elseif (is_singular()) {
			$desc = $this->get_postmeta('description');
			$kw = $this->get_postmeta('keywords');	
		}
		
		//Do we have a description? If so, output it.
		if ($desc) {
			$desc = su_esc_attr($desc);
			echo "\t<meta name=\"description\" content=\"$desc\" />\n";
		}
		
		//Do we have keywords? If so, output them.
		if ($kw) {
			$kw = su_esc_attr($kw);
			echo "\t<meta name=\"keywords\" content=\"$kw\" />\n";
		}
		
		//Supported meta tags and their names
		$verify = array(
			  'google' => 'google-site-verification'
			, 'yahoo' => 'y_key'
			, 'microsoft' => 'msvalidate.01'
		);
		
		//Do we have verification tags? If so, output them.
		foreach ($verify as $site => $name) {
			if ($value = $this->get_setting($site.'_verify')) {
				$value = su_esc_attr($value);
				echo "\t<meta name=\"$name\" content=\"$value\" />\n";
			}
		}
	}
	
	function postmeta_fields($fields) {
		$id = "_su_description";
		$value = attribute_escape($this->get_postmeta('description'));
		
		$fields['20|description|keywords'] =
			  "<tr class='textarea' valign='top'>\n<th scope='row'><label for='$id'>".__('Meta Description:', 'seo-ultimate')."</label></th>\n"
			. "<td><textarea name='$id' id='$id' type='text' class='regular-text' cols='60' rows='3' tabindex='2'"
			. " onkeyup=\"javascript:document.getElementById('su_meta_description_charcount').innerHTML = document.getElementById('_su_description').value.length\">$value</textarea>"
			. "<br />".sprintf(__("You&#8217;ve entered %s characters. Most search engines use up to 160.", 'seo-ultimate'), "<strong id='su_meta_description_charcount'>".strlen($value)."</strong>")
			. "</td>\n</tr>\n"
			. $this->get_postmeta_textbox('keywords', __('Meta Keywords:<br /><em>(separate with commas)</em>', 'seo-ultimate'))
		;
		
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __("<strong>Description:</strong> &mdash; The value of the meta description tag. The description will often appear underneath the title in search engine results. ".
			"Writing an accurate, attention-grabbing description for every post is important to ensuring a good search results clickthrough rate.", 'seo-ultimate');
		$help[] = __("<strong>Keywords:</strong> &mdash; The value of the meta keywords tag. The keywords list gives search engines a hint as to what this post/page is about. ".
			"Be sure to separate keywords with commas, like so: <samp>one,two,three</samp>.", 'seo-ultimate');
		return $help;
	}
	
}

}
?>