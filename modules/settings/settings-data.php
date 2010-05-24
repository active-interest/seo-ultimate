<?php
/**
 * Settings Data Manager Module
 * 
 * @since 2.1
 */

if (class_exists('SU_Module')) {

class SU_SettingsData extends SU_Module {

	function get_parent_module() { return 'settings'; }
	function get_child_order() { return 20; }
	function is_independent_module() { return false; }
	
	function get_module_title() { return __('Settings Data Manager', 'seo-ultimate'); }
	function get_module_subtitle() { return __('Manage Settings Data', 'seo-ultimate'); }
	
	function get_admin_page_tabs() {
		return array(
			  __('Import', 'seo-ultimate') => 'import_tab'
			, __('Export', 'seo-ultimate') => 'export_tab'
			, __('Reset', 'seo-ultimate') => 'reset_tab'
		);
	}
	
	function portable_options() {
		return array('settings', 'modules');
	}
	
	function init() {
		
		if ($this->is_action('su-export')) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="SEO Ultimate Settings ('.date('Y-m-d').').dat"');
			
			$options = $this->portable_options();
			$export = array();
			foreach ($options as $option) {
				$data = $this->plugin->dbdata[$option];
				$data = apply_filters("su_{$option}_export_array", $data);
				$export[$option] = $data;
			}
			$export = base64_encode(serialize($export));
			
			echo $export;
			die();
			
		} elseif ($this->is_action('su-import')) {
			
			if (strlen($_FILES['settingsfile']['name'])) {
			
				$file = $_FILES['settingsfile']['tmp_name'];			
				if (is_uploaded_file($file)) {
					$import = base64_decode(file_get_contents($file));
					if (is_serialized($import)) {
						$import = unserialize($import);
						
						$options = $this->portable_options();
						foreach ($options as $option) {
							$this->plugin->dbdata[$option] = array_merge($this->plugin->dbdata[$option], $import[$option]);
						}
						
						$this->queue_message('success', __("Settings successfully imported.", 'seo-ultimate'));
					} else
						$this->queue_message('error', __("The uploaded file is not in the proper format. Settings could not be imported.", 'seo-ultimate'));
				} else
					$this->queue_message('error', __("The settings file could not be uploaded successfully.", 'seo-ultimate'));
					
			} else
				$this->queue_message('warning', __("Settings could not be imported because no settings file was selected. Please click the &#8220;Browse&#8221; button and select a file to import.", 'seo-ultimate'));
			
		} elseif ($this->is_action('su-reset')) {
			
			$this->plugin->dbdata['settings'] = array();
			unset($this->plugin->dbdata['modules']);
			$this->load_default_settings();
		}
	}
	
	function import_tab() {
		$this->print_messages();
		$this->admin_subheader(__('Import SEO Ultimate Settings File', 'seo-ultimate'));
		$hook = $this->plugin->key_to_hook($this->get_module_or_parent_key());
		echo "\n<p>";
		_e("You can use this form to upload and import an SEO Ultimate settings file stored on your computer. (Settings files can be created using the Export tool.)", 'seo-ultimate');
		echo "</p>\n";
		echo "<form enctype='multipart/form-data' method='post' action='?page=$hook&amp;action=import#su-import'>\n";
		echo "\t<input name='settingsfile' type='file' /> ";
		$confirm = __("Are you sure you want to import this settings file? This will overwrite your current settings and cannot be undone.", 'seo-ultimate');
		echo "<input type='submit' class='button-primary' value='".__("Import", 'seo-ultimate')."' onclick=\"javascript:return confirm('$confirm')\" />\n";
		wp_nonce_field($this->get_nonce_handle('su-import'));
		echo "</form>\n";
		
		//Import from other plugins
		$importmodules = array();
		foreach ($this->plugin->modules as $key => $x_module) {
			$module =& $this->plugin->modules[$key];
			if (is_a($module, 'SU_ImportModule')) {
				$importmodules[$key] =& $module;
			}
		}
		
		if (count($importmodules)) {
			$this->admin_subheader(__("Import from Other Plugins", 'seo-ultimate'));
			echo "\n<p>";
			_e("You can import settings and data from these plugins. Clicking a plugin&#8217;s name will take you to the importer page, where you can customize parameters and start the import.", 'seo-ultimate');
			echo "</p>\n";
			echo "<table class='widefat'>\n";
			
			$class = '';
			foreach ($importmodules as $key => $x_module) {
				$module =& $importmodules[$key];
				$title = $module->get_op_title();
				$desc = $module->get_import_desc();
				$url = $module->get_admin_url();
				$class = ($class) ? '' : 'alternate';
				echo "\t<tr class='$class'><td><a href='$url'>$title</a></td><td>$desc</td></tr>\n";
			}
			
			echo "</table>\n";
		}
	}
	
	function export_tab() {
		echo "\n<p>";
		_e("You can use the export tool to download an SEO Ultimate settings file to your computer.", 'seo-ultimate');
		echo "</p>\n<p>";
		_e("A settings file includes the data of every checkbox and textbox of every installed module. It does NOT include site-specific data like logged 404s or post/page title/meta data (this data would be included in a standard database backup, however).", 'seo-ultimate');
		echo "</p>\n<p>";
		$url = $this->get_nonce_url('su-export');
		echo "<a href='$url' class='button-primary'>".__("Download Settings File", 'seo-ultimate')."</a>";
		echo "</p>\n";
	}
	
	function reset_tab() {
		if ($this->is_action('su-reset'))
			$this->print_message('success', __("All settings have been erased and defaults have been restored.", 'seo-ultimate'));
		echo "\n<p>";
		_e("You can erase all your SEO Ultimate settings and restore them to &#8220;factory defaults&#8221; by clicking the button below.", 'seo-ultimate');
		echo "</p>\n<p>";
		$url = $this->get_nonce_url('su-reset');
		$confirm = __("Are you sure you want to erase all module settings? This cannot be undone.", 'seo-ultimate');
		echo "<a href='$url#su-reset' class='button-primary' onclick=\"javascript:return confirm('$confirm')\">".__("Restore Default Settings", 'seo-ultimate')."</a>";
		echo "</p>\n";
	}
	
	/*
	function admin_page_contents() {
		
		echo "<p>";
		_e("Here you can export, import, and reset the settings of the plugin and all its modules.", 'seo-ultimate');
		echo "</p><p>";
		_e("A settings file includes the data of every checkbox and textbox of every installed module, as well as the &#8220;Plugin Settings&#8221; section above. It does NOT include site-specific data like logged 404s or post/page title/meta data (this data would be included in a standard database backup, however).", 'seo-ultimate');
		echo "</p>";
		
		//Begin table
		echo "<table id='manage-settings'>\n";
		
		//Export
		echo "<tr><th scope='row'>";
		_e("Export:", 'seo-ultimate');
		echo "</th><td>";
		$url = $this->get_nonce_url('su-export');
		echo "<a href='$url' class='button-secondary'>".__("Download Settings File", 'seo-ultimate')."</a>";
		echo "</td></tr>";
		
		//Import
		echo "<tr><th scope='row'>";
		_e("Import:", 'seo-ultimate');
		echo "</th><td>";
		$hook = $this->plugin->key_to_hook($this->get_module_key());
		echo "<form enctype='multipart/form-data' method='post' action='?page=$hook&amp;action=import'>\n";
		echo "\t<input name='settingsfile' type='file' /> ";
		$confirm = __("Are you sure you want to import this settings file? This will overwrite your current settings and cannot be undone.", 'seo-ultimate');
		echo "<input type='submit' class='button-secondary' value='".__("Import This Settings File", 'seo-ultimate')."' onclick=\"javascript:return confirm('$confirm')\" />\n";
		wp_nonce_field($this->get_nonce_handle('su-import'));
		echo "</form>\n";
		echo "</td></tr>";
		
		//Reset
		echo "<tr><th scope='row'>";
		_e("Reset:", 'seo-ultimate');
		echo "</th><td>";
		$url = $this->get_nonce_url('su-reset');
		$confirm = __("Are you sure you want to erase all module settings? This cannot be undone.", 'seo-ultimate');
		echo "<a href='$url' class='button-secondary' onclick=\"javascript:return confirm('$confirm')\">".__("Restore Default Settings", 'seo-ultimate')."</a>";
		echo "</td></tr>";
		
		//End table
		echo "</table>";
		
		//Import from other plugins
		$importmodules = array();
		foreach ($this->plugin->modules as $key => $x_module) {
			$module =& $this->plugin->modules[$key];
			if (is_a($module, 'SU_ImportModule')) {
				$importmodules[$key] =& $module;
			}
		}
		
		if (count($importmodules)) {
			$this->admin_subheader(__("Import from Other Plugins", 'seo-ultimate'));
			echo "\n<p>";
			_e("You can import settings and data from these plugins. Clicking a plugin&#8217;s name will take you to the importer page, where you can customize parameters and start the import.", 'seo-ultimate');
			echo "</p>\n";
			echo "<table class='widefat'>\n";
			
			$class = '';
			foreach ($importmodules as $key => $x_module) {
				$module =& $importmodules[$key];
				$title = $module->get_op_title();
				$desc = $module->get_import_desc();
				$url = $module->get_admin_url();
				$class = ($class) ? '' : 'alternate';
				echo "\t<tr class='$class'><td><a href='$url'>$title</a></td><td>$desc</td></tr>\n";
			}
			
			echo "</table>\n";
		}
	}*/
}

}

?>