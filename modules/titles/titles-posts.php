<?php
/**
 * Post Title Editor Module
 * 
 * @version 1.0
 * @since 1.5
 */

if (class_exists('SU_Module')) {

class SU_TitlesPosts extends SU_Module {

	function get_parent_module() { return 'titles'; }
	function get_child_order() { return 20; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Post Title Editor', 'seo-ultimate'); }
	
	function get_admin_page_tabs() {
	
		$type_keys = array('post', 'page');
		
		$type_labels = array(
			  'post' => __('Posts')
			, 'page' => __('Pages')
			, 'attachment' => __('Attachments')
		);
		
		return $this->parent_module->get_object_subtype_tabs('post', $type_keys, $type_labels, array(&$this, 'admin_page_tab'));
	}
	
	function admin_page_tab($post_type) {
		$this->parent_module->title_editing_table($post_type, 'get_posts', 'post_type='.$post_type.'&numberposts=%1$d&offset=%2$d');
	}

}

}

?>