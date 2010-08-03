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
		return array(__('Blog Homepage') => 'home_tab');
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