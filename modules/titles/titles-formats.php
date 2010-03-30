<?php
class SU_TitlesFormats extends SU_Module {

	function get_parent_module() { return 'titles'; }
	function get_child_order() { return 10; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Title Rewriter Formats', 'seo-ultimate'); }
	function get_module_subtitle() { return __('Default Formats', 'seo-ultimate'); }
	
	function admin_page_contents() {
		echo "<table class='form-table'>\n";
		$this->textboxes($this->parent_module->get_supported_settings(), $this->parent_module->get_default_settings());
		echo "</table>";
	}
}
?>