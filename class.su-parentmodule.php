<?php
/**
 * Parent Module
 * 
 * @abstract
 * @version 1.0
 * @since 0.7
 */

if (class_exists('SU_Module')) {

class SU_ParentModule extends SU_Module {
	
	var $modules = array();
	
	function init() {
		global $seo_ultimate;
		foreach ($seo_ultimate->modules as $key => $module) {
			if ($module->get_parent_module() == $this->get_module_key()) {
				$this->modules[$key] = $module;
			}
		}
	}
	
	function admin_page_contents() {
		$this->admin_form_start(false, false);
		
		foreach ($this->modules as $key => $module) {
			echo "\n<div id='".SEO_Ultimate::key_to_hook($key)."'>\n";
			$this->admin_subheader($module->get_page_title());
			$module->admin_page_contents();
			echo "</div>\n\n";
		}
		
		$this->admin_form_end(false, false);
	}
	
	function admin_help() {
		$help = '';
		
		foreach ($this->modules as $key => $module) {
			if ($childhelp = $module->admin_help())
				$help .= "\n<h6>".$module->get_page_title()."</h6>\n$childhelp\n";
		}
		
		if (strlen($help)) return $help; else return false;
	}
	
}

}
?>