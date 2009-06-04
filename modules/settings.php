<?php
/**
 * Settings Module
 * 
 * @version 1.0
 * @since 0.2
 */

if (class_exists('SU_Module')) {

class SU_Settings extends SU_Module {

	var $wp_meta_called = false;

	function get_page_title() { return __('SEO Ultimate Plugin Settings', 'seo-ultimate'); }
	function get_menu_title() { return __('SEO Ultimate', 'seo-ultimate'); }
	function get_menu_parent(){ return 'options-general.php'; }

	function get_default_settings() {
		return array(
			  'attribution_link' => true
		);
	}
	
	function init() {
		
		if ($this->is_action('export')) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="SEO Ultimate Settings ('.date('Y-m-d').').dat"');
			
			$export = maybe_unserialize(get_option('su_settings'));
			$export = apply_filters('su_settings_export_array', $export);
			$export = base64_encode(serialize($export));
			
			echo $export;
			die();
		}
		
		//Hook to add attribution link
		if ($this->get_setting('attribution_link', true)) {
			add_action('wp_meta', array($this, 'meta_link'));
			add_action('wp_footer', array($this, 'footer_link'));
		}
	}
	
	function admin_page_contents() {
		
		//Plugin Settings
		$this->admin_form_start(__("Plugin Settings", 'seo-ultimate'));
		$this->checkboxes(array(
			  'attribution_link' => __("Enable attribution link", 'seo-ultimate')
			//, 'debug_mode' => __("Enable debug-mode logging", 'seo-ultimate')
			, 'mark_code' => __("Insert comments around HTML code insertions", 'seo-ultimate')
		));
		$this->admin_form_end();
		
		/*//Manage Settings
		$this->admin_subheader(__("Manage Settings Data", 'seo-ultimate'));
		
		echo "<p>";
		_e("This section allows you to export, import, and reset the settings of the plugin and all its modules.", 'seo-ultimate');
		echo "</p>";
		
		if ($this->is_action('import')) {
			
			$file = $_FILES['settingsfile']['tmp_name'];			
			if (is_uploaded_file($file)) {
				$settings = base64_decode(file_get_contents($file));
				if (is_serialized($settings)) {
					update_option('su_settings', $settings);
					$this->print_message('success', __("Settings successfully imported.", 'seo-ultimate'));
				} else
					$this->print_message('error', __("The uploaded file is not in the proper format. Settings could not be imported.", 'seo-ultimate'));
			} else
				$this->print_message('error', __("The settings file could not be uploaded successfully.", 'seo-ultimate'));
			
		} elseif ($this->is_action('reset')) {
			
			update_option('su_settings', serialize(array()));
			delete_option('su_modules');
			$this->print_message('success', __("All settings have been erased.", 'seo-ultimate'));
		}
		
		//Begin table
		echo "<table id='manage-settings'>\n";
		
		//Export
		echo "<tr><th scope='row'>";
		_e("Export:", 'seo-ultimate');
		echo "</th><td>";
		$url = $this->get_nonce_url('export');
		echo "<a href='$url' class='button-secondary'>".__("Download Settings File", 'seo-ultimate')."</a>";
		echo "</td></tr>";
		
		//Import
		echo "<tr><th scope='row'>";
		_e("Import:", 'seo-ultimate');
		echo "</th><td>";
		$hook = SEO_Ultimate::key_to_hook($this->get_module_key());
		echo "<form enctype='multipart/form-data' method='post' action='?page=$hook&amp;action=import'>\n";
		echo "\t<input name='settingsfile' type='file' /> ";
		$confirm = __("Are you sure you want to import this settings file? This will erase all current settings and cannot be undone.", 'seo-ultimate');
		echo "<input type='submit' class='button-secondary' value='".__("Import This Settings File", 'seo-ultimate')."' onclick=\"javascript:return confirm('$confirm')\" />\n";
		wp_nonce_field($this->get_nonce_handle('import'));
		echo "</form>\n";
		echo "</td></tr>";
		
		//Reset
		echo "<tr><th scope='row'>";
		_e("Reset:", 'seo-ultimate');
		echo "</th><td>";
		$url = $this->get_nonce_url('reset');
		$confirm = __("Are you sure you want to erase all module settings? This cannot be undone.", 'seo-ultimate');
		echo "<a href='$url' class='button-secondary' onclick=\"javascript:return confirm('$confirm')\">".__("Restore Default Settings", 'seo-ultimate')."</a>";
		echo "</td></tr>";
		
		//End table
		echo "</table>";*/
	}
	
	function meta_link() {
		echo "<li><a href='http://www.seodesignsolutions.com/' title='Search engine optimization technology by SEO Design Solutions'>SEO</a></li>\n";
		$this->wp_meta_called = true;
	}
	
	function footer_link() {
		if (!$this->wp_meta_called)
			echo "\n<p><a href='http://www.seodesignsolutions.com/'>SEO</a> optimization provided by SEO Design Solutions</a></p>\n";
	}
	
	function admin_help() {
		return __(<<<STR
<p>The Settings module lets you manage settings related to the SEO Ultimate plugin as a whole.</p>
<p>Here&#8217;s information on each of the settings:</p>
<ul>
	<li><p><strong>Enable attribution link</strong> &mdash; If enabled, the plugin will display an attribution link on your site.
		We ask that you please leave this enabled.</p></li>
	<li><p><strong>Insert comments around HTML code insertions</strong> &mdash; If enabled, SEO Ultimate will use HTML comments to identify all code it inserts into your &lt;head&gt; tag.
		This is useful if you&#8217;re trying to figure out whether or not SEO Ultimate is inserting a certain piece of header code.</p></li>
</ul>
STR
, 'seo-ultimate');
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