<?php
/**
 * Import Module
 * 
 * @abstract
 * @since 1.5
 */

if (class_exists('SU_Module')) {

class SU_ImportModule extends SU_Module {
	
	var $error = false;
	
	function get_menu_parent() { return 'su-import-modules'; }
	
	function get_op_title() { return $this->get_module_title(); }
	function get_import_desc() { return ''; }
	
	function admin_page() {
		$this->admin_page_start('tools');
		
		if ($this->is_action('update')) {
			ob_start();
			$this->admin_page_contents();
			ob_end_clean();
			
			$this->import_page_contents();
		} else
			$this->admin_page_contents();
		
		$this->admin_page_end();
	}
	
	function admin_form_end($button = null, $table = true) {
		if ($button === null) $button = __("Import Now", 'seo-ultimate');
		parent::admin_form_end($button, $table);
		
		$this->print_message('warning', sprintf(__('The import cannot be undone. It is your responsibility to <a href="%s" target="_blank">backup your database</a> before proceeding!', 'seo-ultimate'), suwp::get_backup_url()));
	}
	
	function import_page_contents() {
		
		//echo "<table id='import-status'>\n";
		echo "<div id='import-status'>\n";
		$this->do_import();
		
		if (!$this->error)
			$this->import_status('success', __("Import complete.", 'seo-ultimate'));
		
		echo "</div>\n";
		//echo "</table>\n";
		
		if ($this->error) {
			echo '<p><a href="admin.php?page=su-import-aiosp" class="button-secondary">';
			_e('Return to import page', 'seo-ultimate');
		} elseif ($this->plugin->module_exists('settings')) {
			echo '<p><a href="options-general.php?page=seo-ultimate" class="button-secondary">';
			_e('Return to settings page', 'seo-ultimate');
		} else {
			echo '<p><a href="admin.php?page=seo" class="button-secondary">';
			_e('Return to SEO page', 'seo-ultimate');
		}
		echo "</a></p>\n";
	}
	
	function import_status($type, $message) {
		if (strcmp($type, 'error') == 0) $this->error = true;
		$this->print_mini_message($type, $message);
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