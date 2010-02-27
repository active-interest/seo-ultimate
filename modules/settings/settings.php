<?php
/**
 * SEO Ultimate Plugin Settings Module
 * 
 * @version 2.3.1
 * @since 0.2
 */

if (class_exists('SU_Module')) {

class SU_Settings extends SU_Module {
	
	var $wp_meta_called = false;
	
	function get_module_title() { return __('Plugin Settings', 'seo-ultimate'); }
	function get_page_title() { return __('SEO Ultimate Plugin Settings', 'seo-ultimate'); }
	function get_menu_title() { return __('SEO Ultimate', 'seo-ultimate'); }
	function get_menu_parent(){ return 'options-general.php'; }
	
	function get_default_settings() {
		return array(
			  'plugin_notices' => true
			, 'log_hits' => true
			, 'delete_old_hits_value' => 30
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
			
			$this->queue_message('success', __("All settings have been erased and defaults have been restored.", 'seo-ultimate'));
		}
		
		
		//Hook to add attribution link
		if ($this->get_setting('attribution_link', true)) {
			add_action('wp_meta', array(&$this, 'meta_link'));
			add_action('wp_footer', array(&$this, 'footer_link'));
		}
	}
	
	function admin_page_contents() {
		
		//Plugin Settings
		$this->admin_form_start(__("Plugin Settings", 'seo-ultimate'));
		$this->checkboxes(array(
			  'attribution_link' => __("Enable attribution link", 'seo-ultimate')
			, 'attribution_link_css' => __("Enable attribution link CSS styling", 'seo-ultimate')
			, 'plugin_notices' => __("Notify me about unnecessary active plugins", 'seo-ultimate')
			//, 'debug_mode' => __("Enable debug-mode logging", 'seo-ultimate')
			, 'mark_code' => __("Insert comments around HTML code insertions", 'seo-ultimate')
			, 'log_hits' => __("Allow modules to save visitor information to the database", 'seo-ultimate')
			, 'delete_old_hits' => __("Delete logged visitor information after %d days", 'seo-ultimate')
		));
		$this->admin_form_end();
		
		//Manage Settings
		$this->admin_subheader(__("Manage Settings Data", 'seo-ultimate'));
		$this->print_messages();
		
		echo "<p>";
		_e("This section allows you to export, import, and reset the settings of the plugin and all its modules.", 'seo-ultimate');
		echo "</p><p>";
		_e("A settings file includes the data of every checkbox and textbox of every installed module, as well as the &#8220;Plugin Settings&#8221; section above. ".
			"It does NOT include site-specific data like logged 404s or post/page title/meta data (this data would be included in a standard database backup, however).", 'seo-ultimate');
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
	}
	
	function meta_link() {
		echo "<li><a href='http://www.seodesignsolutions.com/' title='Search engine optimization technology by SEO Design Solutions'>SEO</a></li>\n";
		$this->wp_meta_called = true;
	}
	
	function footer_link() {
		if (!$this->wp_meta_called) {
			if ($this->get_setting('attribution_link_css')) {
				$pstyle = " style='text-align: center; font-size: smaller;'";
				$astyle = " style='color: inherit;'"; 
			} else $pstyle = $astyle = '';
			
			echo "\n<p id='suattr'$pstyle>Search engine optimization by <a href='http://www.seodesignsolutions.com/'$astyle>SEO Design Solutions</a></a></p>\n";
		}
	}

}

} elseif ($_GET['css'] == 'admin') {
	header('Content-type: text/css');
?>

#su-settings table#manage-settings {
	border-collapse: collapse;
	margin-top: 2em;
}

#su-settings table#manage-settings td {
	width: 100%;
}

#su-settings table#manage-settings th {
	font-weight: bold;
	padding-right: 2em;
}

#su-settings table#manage-settings td,
#su-settings table#manage-settings th {
	padding-top: 2em;
	padding-bottom: 2em;
	border-top: 1px solid #ccc;
}

<?php
}
?>