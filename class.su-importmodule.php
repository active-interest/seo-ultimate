<?php
/**
 * Import Module
 * 
 * @abstract
 * @version 1.0.1
 * @since 0.6
 */

if (class_exists('SU_Module')) {

class SU_ImportModule extends SU_Module {
	
	var $import_status_image;
	
	function get_menu_parent() { return 'su-import-modules'; }
	
	function admin_page() {
		$this->admin_page_start('tools');
		
		if ($this->is_action('update')) {
			ob_start();
			$this->admin_page_contents();
			ob_end_clean();
			
			global $seo_ultimate;
			$this->import_status_image = $seo_ultimate->plugin_dir_url.'images/success.png';
			
			$this->import_page_contents();
		} else
			$this->admin_page_contents();
		
		$this->admin_page_end();
	}
	
	function admin_form_end($button = false, $table = true) {
		if (!$button) $button = __("Import Now", 'seo-ultimate');
		parent::admin_form_end($button, $table);
	}
	
	function import_page_contents() {
		
		echo "<table id='import-status'>\n";
		$this->do_import();
		echo "</table>\n";
		if ($this->module_exists('settings')) {
			echo '<a href="options-general.php?page=seo-ultimate" class="button-secondary">';
			_e('Return to settings page', 'seo-ultimate');
		} else {
			echo '<a href="admin.php?page=seo" class="button-secondary">';
			_e('Return to SEO page', 'seo-ultimate');
		}
		echo "</a>\n";
	}
	
	function import_status($message) {
		echo "<tr><td class='image'><img src='{$this->import_status_image}' alt='' /></td><td class='message'>$message</td></tr>";
	}
	
	function import_option($module, $key, $option) {
		if (!isset($this->settings[$module][$key]) || $this->get_setting('overwrite_su')) {
			$this->settings[$module][$key] = get_option($option);
			if ($this->get_setting('delete_import')) delete_option($option);
		}
	}
}

}
?>