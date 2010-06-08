<?php
/**
 * Post Title Editor Module
 * 
 * @since 1.5
 */

if (class_exists('SU_Module')) {

class SU_TitlesPosts extends SU_Module {

	function get_parent_module() { return 'titles'; }
	function get_child_order() { return 20; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Post Title Editor', 'seo-ultimate'); }
	
	function get_admin_page_tabs() {
		
		$types = array();
		
		//Custom post type support - requires WordPress 3.0 or above (won't work with 2.9 custom post types)
		if (function_exists('get_post_types'))
			$types = suarr::flatten_values(get_post_types(array('public' => true), 'objects'), array('labels', 'name'));
		
		//Legacy support for WordPress 2.9 and below
		if (!$types)
			$types = array(
				  'post' => __('Posts')
				, 'page' => __('Pages')
				, 'attachment' => __('Attachments')
			);
		
		return $this->parent_module->get_object_subtype_tabs('post', array_keys($types), array_values($types), array(&$this, 'admin_page_tab'));
	}
	
	function admin_page_tab($post_type) {
		$this->parent_module->title_editing_table($post_type, 'get_posts', 'post_type='.$post_type.'&numberposts=%1$d&offset=%2$d');
	}

}

}

?>