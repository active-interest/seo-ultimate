<?php
/**
 * Taxonomy Title Editor Module
 * 
 * @since 1.9
 */

if (class_exists('SU_Module')) {

class SU_TitlesTaxonomies extends SU_Module {

	function get_parent_module() { return 'titles'; }
	function get_child_order() { return 30; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Taxonomy Title Editor', 'seo-ultimate'); }
	
	function get_admin_page_tabs() {
		
		$type_keys = array('category', 'post_tag');
		
		$type_labels = array(
			  'category' => __('Categories')
			, 'post_tag' => __('Post Tags')
		);
		
		return $this->parent_module->get_object_subtype_tabs('taxonomy', $type_keys, $type_labels, array(&$this, 'admin_page_tab'));
	}
	
	function admin_page_tab($tax_type) {
		$this->parent_module->title_editing_table($tax_type, 'get_terms', array($tax_type, 'number=%1$d&offset=%2$d'), 'taxonomy',
			'term_id', 'name', array('suwp', 'get_taxonomy_link'));
	}

}

}

?>