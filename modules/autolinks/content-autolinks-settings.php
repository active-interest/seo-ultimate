<?php
/**
 * Content Deeplink Juggernaut Settings Module
 * 
 * @since 2.2
 */

if (class_exists('SU_Module')) {

class SU_ContentAutolinksSettings extends SU_Module {
	
	function get_parent_module() { return 'autolinks'; }
	function get_child_order() { return 20; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Content Deeplink Juggernaut Settings', 'seo-ultimate'); }
	function get_module_subtitle() { return __('Content Link Settings', 'seo-ultimate'); }
	
	function admin_page_contents() {
		$this->admin_form_table_start();
		$this->checkbox('limit_lpp', __('Don&#8217;t add any more than %d autolinks per post/page/etc.', 'seo-ultimate'));
		$this->admin_form_table_end();
	}
}

}
?>