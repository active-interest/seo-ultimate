<?php
/**
 * Meta Editor Settings Module
 * 
 * @since 1.5
 */

if (class_exists('SU_Module')) {

class SU_MetaSettings extends SU_Module {
	
	function get_parent_module() { return 'meta'; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Meta Editor Settings', 'seo-ultimate'); }
	function get_module_subtitle() { return __('Settings', 'seo-ultimate'); }
	
	function get_default_settings() {
		return array(
			'home_description_tagline_default' => true
		);
	}
	
	function admin_page_contents() {
		$this->admin_form_table_start();
		$this->textareas(array(
			  'home_description' => __("Blog Homepage Meta Description", 'seo-ultimate')
			, 'home_keywords' => __("Blog Homepage Meta Keywords", 'seo-ultimate')
		), 3);
		$this->checkboxes(array(
				  'home_description_tagline_default' => __("Use this blog&#8217s tagline as the default homepage description.", 'seo-ultimate')
			), __("Default Values", 'seo-ultimate'));
		$this->checkboxes(array(
				  'noodp' => __("Don&#8217t use this site&#8217s Open Directory description in search results.", 'seo-ultimate')
				, 'noydir' => __("Don&#8217t use this site&#8217s Yahoo! Directory description in search results.", 'seo-ultimate')
				, 'noarchive' => __("Don&#8217t cache or archive this site.", 'seo-ultimate')
			), __("Spider Instructions", 'seo-ultimate'));
		$this->textboxes(array(
				  'google_verify' => __("Google Webmaster Tools:", 'seo-ultimate')
				, 'yahoo_verify' => __("Yahoo! Site Explorer:", 'seo-ultimate')
				, 'microsoft_verify' => __("Bing Webmaster Center:", 'seo-ultimate')
			), array(), __("Verification Codes", 'seo-ultimate'));
		$this->textarea('custom_html', __("Custom &lt;head&gt; HTML", 'seo-ultimate'));
		$this->admin_form_table_end();
	}
}

}
?>