<?php
/**
 * 404 Monitor Settings Module
 * 
 * @since 2.1
 */

if (class_exists('SU_Module')) {

class SU_FofsSettings extends SU_Module {
	
	function get_parent_module() { return 'fofs'; }
	function get_child_order() { return 20; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('404 Monitor Settings', 'seo-ultimate'); }
	function get_module_subtitle() { return __('Settings', 'seo-ultimate'); }
	function get_settings_key() { return '404s'; }
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->checkbox('log_enabled', __("Continue monitoring for new 404 errors", 'seo-ultimate'));
		$this->admin_form_end();
	}
}

}
?>