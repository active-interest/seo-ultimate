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
	
	function get_default_settings() {
		return array(
			  'enable_self_links' => false
			, 'limit_lpp_value' => 5
			, 'limit_lpa_value' => 2
			, 'limit_sitewide_lpa_value' => 50
			, 'linkfree_tags' => 'code,pre,kbd,h1,h2,h3,h4,h5,h6'
		);
	}
	
	function admin_page_contents() {
		$this->admin_form_table_start();
		
		$this->checkbox('enable_self_links', __('Allow posts to link to themselves.', 'seo-ultimate'), __('Self-Linking', 'seo-ultimate'));
		
		$this->checkboxes(array(
			  'limit_lpp' => __('Don&#8217;t add any more than %d autolinks per post/page/etc.', 'seo-ultimate')
			, 'limit_lpa' => __('Don&#8217;t link the same anchor text any more than %d times per post/page/etc.', 'seo-ultimate')
			, 'limit_sitewide_lpa' => __('Don&#8217;t link the same anchor text any more than %d times across my entire site.', 'seo-ultimate')
		), __('Quantity Restrictions', 'seo-ultimate'));
		
		$this->textbox('linkfree_tags', __('Don&#8217;t add autolinks to text within these HTML tags <em>(separate with commas)</em>:', 'seo-ultimate'), $this->get_default_setting('linkfree_tags'), __('Tag Restrictions', 'seo-ultimate'));
		
		$siloing_checkboxes = array();
		$post_types = get_post_types(array('public' => true), 'objects');
		foreach ($post_types as $post_type) {
			$taxonomies = suwp::get_object_taxonomies($post_type->name);
			if (count($taxonomies)) {
				$siloing_checkboxes['dest_limit_' . $post_type->name] = sprintf(
					  __('%s can only link to internal destinations that share at least one...', 'seo-ultimate')
					, $post_type->labels->name
				);
				
				foreach ($taxonomies as $taxonomy) {
					$siloing_checkboxes['dest_limit_' . $post_type->name . '_within_' . $taxonomy->name] = array(
						  'description' => $taxonomy->labels->singular_name
						, 'indent' => true
					);
				}
			}
		}
		
		$this->checkboxes($siloing_checkboxes, __('Siloing', 'seo-ultimate'));
		
		$this->admin_form_table_end();
	}
}

}
?>