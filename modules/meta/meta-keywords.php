<?php
/**
 * Meta Keywords Editor Module
 * 
 * @since 4.0
 */

if (class_exists('SU_Module')) {

class SU_MetaKeywords extends SU_Module {
	
	function get_module_title() { return __('Meta Keywords Editor', 'seo-ultimate'); }
	function get_menu_title()   { return __('Meta Keywords', 'seo-ultimate'); }
	function get_settings_key() { return 'meta'; }
	
	function init() {
		add_action('su_head', array(&$this, 'head_tag_output'));
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 20);
	}
	
	function get_admin_page_tabs() {
		return array_merge(
			  array(
				  __('Default Values', 'seo-ultimate') => 'defaults_tab'
				, __('Blog Homepage', 'seo-ultimate') => 'home_tab'
				)
			, $this->get_postmeta_edit_tabs(array(
				  'type' => 'textbox'
				, 'name' => 'keywords'
				, 'term_settings_key' => 'taxonomy_keywords'
				, 'label' => __('Meta Keywords', 'seo-ultimate')
			))
		);
	}
	
	function defaults_tab() {
		$this->admin_form_table_start();
		$this->textarea('global_keywords', __('Sitewide Keywords', 'seo-ultimate') . '<br /><small><em>' . __('(Separate with commas)', 'seo-ultimate') . '</em></small>');
		$this->admin_form_table_end();
	}
	
	function home_tab() {
		$this->admin_form_table_start();
		$this->textarea('home_keywords', __('Blog Homepage Meta Keywords', 'seo-ultimate'), 3);
		$this->admin_form_table_end();
	}
	
	function head_tag_output() {
		
		$kw = false;
		
		//If we're viewing the homepage, look for homepage meta data.
		if (is_home()) {
			$kw = $this->get_setting('home_keywords');
		
		//If we're viewing a post or page, look for its meta data.
		} elseif (is_singular()) {
			$kw = $this->get_postmeta('keywords');	
		
		//If we're viewing a term, look for its meta data.
		} elseif (is_category() || is_tag() || is_tax()) {
			global $wp_query;
			$tax_keywords = $this->get_setting('taxonomy_keywords');
			$kw = $tax_keywords[$wp_query->get_queried_object_id()];
		}
		
		if ($globals = $this->get_setting('global_keywords')) {
			if (strlen($kw)) $kw .= ',';
			$kw .= $globals;
		}
		
		$kw = str_replace(array("\r\n", "\n"), ',', $kw);
		$kw = explode(',', $kw);
		$kw = array_map('trim', $kw); //Remove extra spaces from beginning/end of keywords
		$kw = array_filter($kw); //Remove blank keywords
		$kw = array_unique($kw); //Remove duplicate keywords
		$kw = implode(',', $kw);
		
		//Do we have keywords? If so, output them.
		if ($kw) {
			$kw = su_esc_attr($kw);
			echo "\t<meta name=\"keywords\" content=\"$kw\" />\n";
		}
	}
	
	function postmeta_fields($fields) {	
		$fields['25|keywords'] = $this->get_postmeta_textbox('keywords', __('Meta Keywords:<br /><em>(separate with commas)</em>', 'seo-ultimate'));
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __('<strong>Keywords</strong> &mdash; The value of the meta keywords tag. The keywords list gives search engines a hint as to what this post/page is about. Be sure to separate keywords with commas, like so: <samp>one,two,three</samp>.', 'seo-ultimate');
		return $help;
	}
	
}

}
?>