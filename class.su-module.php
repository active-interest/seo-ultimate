<?php
/**
 * The pseudo-abstract class upon which all modules are based.
 * 
 * @abstract
 * @version 1.4
 * @since 0.1
 */
class SU_Module {
	
	/********** VARIABLES **********/
	
	var $module_key;
	
	/**
	 * Stores the module file's URL.
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $module_url;
	
	/**
	 * Stores the module's plugin page hook (the full hook with seo_page_ prefix).
	 * A reconstructed value of the get_plugin_page_hook() function, which is only available after admin init.
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $plugin_page_hook;
	
	/**
	 * Contains messages that are waiting to be displayed to the user.
	 * 
	 * @since 0.1
	 * @var array
	 */
	var $messages = array();
	
	
	/********** CONSTRUCTOR FUNCTION **********/
	
	/**
	 * PHP4 constructor that points to the likely-overloaded PHP5 constructor.
	 * 
	 * @since 0.1
	 * @uses __construct()
	 */
	function SU_Module() {
		$this->__construct();
	}
	
	
	/********** PSEUDO-ABSTRACT FUNCTIONS **********/
	
	/**
	 * PHP5 constructor.
	 * 
	 * @since 0.1
	 */
	function __construct() { }
	
	/**
	 * The title of the admin page, which is displayed in the <title> and <h2> tags.
	 * Is the same as the menu title by default.
	 * 
	 * @since 0.1
	 * 
	 * @return string The title shown on this module's admin page.
	 */
	function get_page_title() { return $this->get_menu_title(); }
	
	/**
	 * The title that appears on the administration navigation menu.
	 * 
	 * @since 0.1
	 * 
	 * @return string The title shown on the admin menu.
	 */
	function get_menu_title() { return ''; }
	
	/**
	 * Determines where this module's admin page should appear relative to those of other modules.
	 * If two modules have the same menu position index, they are sorted alphabetically.
	 * 
	 * @since 0.1
	 * 
	 * @return int The menu position index.
	 */
	function get_menu_pos()   { return 999; }
	
	/**
	 * The number that should be displayed in a bubble next to the module's menu title.
	 * A return value of zero means no bubble is shown.
	 * 
	 * @since 0.1
	 * 
	 * @return int The number that should be displayed.
	 */
	function get_menu_count() { return 0; }
	
	/**
	 * A descriptive label of the menu count.
	 * 
	 * @since 0.3
	 * 
	 * @return string The label.
	 */
	function get_menu_count_label() { return ''; }
	
	/**
	 * Indicates under which top-level menu this module's admin page should go.
	 * Examples: seo (This plugin's SEO menu), options-general.php (The Settings menu)
	 * 
	 * @since 0.1
	 * 
	 * @return string The value to pass to WordPress's add_submenu_page() function.
	 */
	function get_menu_parent(){ return 'seo'; }
	
	/**
	 * Returns the hook of this module's menu parent.
	 * Examples: seo (This plugin's SEO menu), settings (The Settings menu), toplevel (The toplevel)
	 * 
	 * @since 0.1
	 * 
	 * @return string The hook of the module's menu parent.
	 */
	function get_menu_parent_hook() { return $this->get_menu_parent(); }
	
	/**
	 * The module key of this module's parent. Defaults to false (no parent).
	 * 
	 * @since 0.3
	 * 
	 * @return string|bool
	 */
	function get_parent_module() { return false; }
	
	/**
	 * Called at WordPress's init hook.
	 * 
	 * @since 0.1
	 */
	function init() {}
	
	/**
	 * Called upon module activation,
	 * i.e. when a module is uploaded or when the plugin is activated for the first time.
	 * 
	 * @since 0.1
	 */
	function activate() { }
	
	/**
	 * Returns an array of default settings. The defaults will be saved in the database if the settings don't exist.
	 * 
	 * @since 0.1
	 * 
	 * @return array The default settings. (The setting name is the key, and the default value is the array value.)
	 */
	function get_default_settings() { return array(); }
	
	/**
	 * The contents of the administration page.
	 * 
	 * @since 0.1
	 */
	function admin_page_contents() { }
	
	/**
	 * Returns the module's custom help content that should go in the "Help" dropdown of WordPress 2.7 and above.
	 * 
	 * @since 0.1
	 * 
	 * @return string|false The help text, or false if no custom help is available.
	 */
	function admin_help() { return false; }
	
	/**
	 * Adds the module's post meta box field HTML to the array.
	 * 
	 * @since 0.1
	 * 
	 * @param array $fields The fields array.
	 * @return array The updated fields array.
	 */
	function postmeta_fields($fields) { return $fields;	}
	
	
	/********** INITIALIZATION FUNCTIONALITY **********/
	
	/**
	 * If settings are unset, apply the defaults if available.
	 * 
	 * @since 0.5
	 * @uses get_default_settings()
	 * @uses get_setting()
	 * @uses update_setting()
	 */
	function load_default_settings() {
		
		$defaults = $this->get_default_settings();
		foreach ($defaults as $setting => $default) {
			if ($this->get_setting($setting, "{reset}") === "{reset}")
				$this->update_setting($setting, $default);
		}
	}
	
	
	/********** MODULE FUNCTIONS **********/
	
	/**
	 * Returns the array key of the module.
	 * 
	 * @since 0.1
	 * @uses $module_key
	 * 
	 * @return string The module key.
	 */
	function get_module_key() {
		if ($this->module_key)
			return $this->module_key;
		else
			die(str_rot13('Zbqhyr ybnqrq sebz na rkgreany fbhepr!'));
	}
	
	/**
	 * Checks to see whether a specified module exists.
	 * 
	 * @since 0.1
	 * @uses $seo_ultimate
	 * 
	 * @param string $key The key of the module to check.
	 * @return boolean Whether the module is loaded into SEO Ultimate.
	 */
	function module_exists($key) {
		global $seo_ultimate;
		return isset($seo_ultimate->modules[$key]);
	}
	
	/**
	 * Returns the absolute URL of the module's admin page.
	 * 
	 * @since 0.7
	 */
	function get_admin_url() {
		if ($key = $this->get_parent_module()) {
			$anchor = '#'.SEO_Ultimate::key_to_hook($this->get_module_key());
		} else {
			$key = $this->get_module_key();
			$anchor = '';
		}
		
		return admin_url('admin.php?page='.SEO_Ultimate::key_to_hook($key).$anchor);
	}
	
	
	/********** SETTINGS FUNCTIONS **********/
	
	/**
	 * Retrieves the given setting from a module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * 
	 * @param string $key The name of the setting to retrieve.
	 * @param mixed $default What should be returned if the setting does not exist. Optional.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module. Optional.
	 * @return mixed The value of the setting, or the $default variable.
	 */
	function get_setting($key, $default=null, $module=null) {
		if (!$module) $module = $this->get_module_key();
		$settings = maybe_unserialize(get_option('su_settings'));
		if (isset($settings[$module][$key]))
			$setting = $settings[$module][$key];
		else
			$setting = $default;
		
		return apply_filters("su_get_setting-$module-$key", $setting);
	}
	
	/**
	 * Sets a value in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * 
	 * @param string $key The key of the setting to be changed.
	 * @param string $value The new value to assign to the setting.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module. Optional.
	 */
	function update_setting($key, $value, $module=null) {
		if (!$module) $module = $this->get_module_key();
		
		if (!apply_filters("su_custom_update_setting-$module-$key", false, $value)) {
			$settings = maybe_unserialize(get_option('su_settings'));
			if (!$settings) $settings = array();
			$settings[$module][$key] = $value;
			update_option('su_settings', serialize($settings));
		}
	}
	
	/**
	 * Adds 1 to the value of an integer setting in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_setting()
	 * @uses update_setting()
	 * 
	 * @param string $key The key of the setting to be incremented.
	 */
	function increment_setting($key) {
		$value = $this->get_setting($key);
		$this->update_setting($key, $value+1);
	}
	
	/**
	 * Assigns a value of zero to a setting in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses update_setting()
	 * 
	 * @param string $key The key of the setting to be reset.
	 */
	function reset_setting($key) {
		$this->update_setting($key, 0);
	}
	
	/**
	 * Updates the value of more than one setting at a time.
	 * 
	 * @since 0.1
	 * @uses update_setting()
	 * 
	 * @param array $settings The names (keys) and values of settings to be updated.
	 */
	function update_settings($settings) {
		foreach ($settings as $key => $value)
			update_setting($key, $value);
	}
	
	
	/********** ADMIN PAGE FUNCTIONS **********/
	
	/**
	 * Displays the beginning, contents, and end of the module's administration page.
	 * 
	 * @since 0.1
	 * @uses admin_page_start()
	 * @uses admin_page_contents()
	 * @uses admin_page_end()
	 */
	function admin_page() {
		$this->admin_page_start();
		$this->admin_page_contents();
		$this->admin_page_end();
	}
	
	/**
	 * Outputs the starting code for an administration page: 
	 * wrapper, ID'd <div>, icon, and title
	 * 
	 * @since 0.1
	 * @uses admin_footer() Hooked into WordPress's in_admin_footer action.
	 * @uses get_module_key()
	 * @uses get_page_title()
	 * 
	 * @param string $icon The ID that should be applied to the icon element. The icon is loaded via CSS based on the ID. Optional.
	 */
	function admin_page_start($icon = 'options-general') {
		
		add_action('in_admin_footer', array($this, 'admin_footer'));
		
		echo "<div class=\"wrap\">\n";
		echo "<div id=\"su-".attribute_escape($this->get_module_key())."\" class=\"su-module\">\n";
		screen_icon($icon);
		echo "\n<h2>".$this->get_page_title()."</h2>\n";
	}
	
	/**
	 * Outputs an administration page subheader (an <h3> tag).
	 * 
	 * @since 0.1
	 * 
	 * @param string $title The text to output.
	 */
	function admin_subheader($title) {
		echo "<h3 class='su-subheader'>$title</h3>\n";
	}
	
	/**
	 * Outputs an administration form table subheader.
	 * 
	 * @since 0.1
	 * 
	 * @param string $title The text to output.
	 */
	function admin_form_subheader($title) {
		echo "<th><strong>$title</strong></th>\n";
	}
	
	/**
	 * Outputs the ending code for an administration page.
	 * 
	 * @since 0.1
	 */
	function admin_page_end() {
		echo "\n</div>\n</div>\n";
	}
	
	/**
	 * Outputs a tab control and loads the current tab.
	 * 
	 * @since 0.7
	 * @uses get_admin_url()
	 * 
	 * @param array $tabs The names of the functions that display the tab contents are the array keys, and the internationalized tab titles are the array values.
	 */
	function admin_page_tabs($tabs = array(), $tabset = 'su-tabset') {
		
		echo "\n\n<div id='$tabset' class='su-tabs'>\n";
		
		foreach ($tabs as $function => $title) {
			echo "<fieldset id='$function'>\n<h3>$title</h3>\n";
			if (is_callable($call = array($this, $function))) call_user_func($call);
			echo "</fieldset>\n";
		}
		echo "</div>\n";
?>

<script type="text/javascript">
/* <![CDATA[ */	
	jQuery(function() 
	{
		su_init_tabs();		
	 });
	
	function su_init_tabs()
	{
		/* if this is not the breadcrumb admin page, quit */
		if (!jQuery("#<?php echo $tabset; ?>").length) return;		

		/* init markup for tabs */
		jQuery('#<?php echo $tabset; ?>').prepend("<ul><\/ul>");
		jQuery('#<?php echo $tabset; ?> > fieldset').each(function(i)
		{
		    id      = jQuery(this).attr('id');
		    caption = jQuery(this).find('h3').text();
		    jQuery('#<?php echo $tabset; ?> > ul').append('<li><a href="#'+id+'"><span>'+caption+"<\/span><\/a><\/li>");
		    jQuery(this).find('h3').hide();					    
	    });
		
		/* init the tabs plugin */
		var jquiver = undefined == jQuery.ui ? [0,0,0] : undefined == jQuery.ui.version ? [0,1,0] : jQuery.ui.version.split('.');
		switch(true) {
			// tabs plugin has been fixed to work on the parent element again.
			case jquiver[0] >= 1 && jquiver[1] >= 7:
				jQuery("#<?php echo $tabset; ?>").tabs();
				break;
			// tabs plugin has bug and needs to work on ul directly.
			default:
				jQuery("#<?php echo $tabset; ?> > ul").tabs(); 
		}

		/* handler for openeing the last tab after submit (compability version) */
		jQuery('#<?php echo $tabset; ?> ul a').click(function(i){
			var form   = jQuery('#bcn_admin_options');
			var action = form.attr("action").split('#', 1) + jQuery(this).attr('href');
			// an older bug pops up with some jQuery version(s), which makes it
			// necessary to set the form's action attribute by standard javascript 
			// node access:						
			form.get(0).setAttribute("action", action);
		});
	}
</script>

<?php
	}
	
	/**
	 * Adds plugin/module information to the admin footer.
	 * 
	 * @since 0.1
	 * @uses SU_PLUGIN_URI
	 * @uses SU_PLUGIN_NAME
	 * @uses SU_AUTHOR_URI
	 * @uses SU_AUTHOR
	 */
	function admin_footer() {
		printf(__('%1$s | %2$s %3$s by %4$s', 'seo-ultimate'),
			$this->get_page_title(),
			'<a href="'.SU_PLUGIN_URI.'" target="_blank">'.__(SU_PLUGIN_NAME, 'seo-ultimate').'</a>',
			SU_VERSION,
			'<a href="'.SU_AUTHOR_URI.'" target="_blank">'.__(SU_AUTHOR, 'seo-ultimate').'</a>'
		);
		
		echo "<br />";
	}
	
	
	/********** ADMIN FORM FUNCTIONS **********/
	
	/**
	 * Begins an administration form.
	 * Outputs a subheader if provided, queues a success message upon settings update, outputs queued messages,
	 * opens a form tag, outputs a nonce field and other WordPress fields, and begins a form table.
	 * 
	 * @since 0.1
	 * @uses SEO_Ultimate::key_to_hook()
	 * @uses get_module_key()
	 * @uses admin_subheader()
	 * @uses is_action()
	 * @uses queue_message()
	 * @uses print_messages()
	 * @uses get_parent_module()
	 * 
	 * @param mixed $header The text of the subheader that should go right before the form. Optional.
	 * @param boolean $table Whether or not to start a form table.
	 */
	function admin_form_start($header = false, $table = true) {
		$hook = SEO_Ultimate::key_to_hook($this->get_module_key());
		if ($header) $this->admin_subheader($header);
		
		if (!$this->get_parent_module()) {
			if ($this->is_action('update')) $this->print_message('success', __('Settings updated.', 'seo-ultimate'));
			echo "<form method='post' action='?page=$hook'>\n";
			settings_fields($hook);
		}
		
		echo "\n";
		if ($table) echo "<table class='form-table'>\n";
	}
	
	/**
	 * Ends an administration form.
	 * Closes the table tag, outputs a "Save Changes" button, and closes the form tag.
	 * 
	 * @since 0.1
	 * @uses get_parent_module()
	 * 
	 * @param string|false $button The label of the submit button.
	 * @param boolean $table Whether or not a form table should be ended.
	 */
	function admin_form_end($button = false, $table = true) {
		if (!$button) $button = __('Save Changes'); //This string is used in normal WP, so we don't need a textdomain
		if ($table) echo "</table>\n";
		
		if (!$this->get_parent_module()) {
?>
<p class="submit">
	<input type="submit" class="button-primary" value="<?php echo $button ?>" />
</p>
</form>
<?php
		}
	}
	
	/**
	 * Applies the necessary HTML so that certain content is displayed only when the mouse hovers over the including table row.
	 * 
	 * @since 0.1
	 * 
	 * @param string $text The always-visible text.
	 * @param string $hovertext The text that only displays upon row hover.
	 * @return string The HTML to put in a hover-supporting table row.
	 */
	function hover_row($text, $hovertext) {
		return "<div>$text</div>\n<div class='row-actions'>$hovertext</div>";
	}
	
	/**
	 * Outputs a group of checkboxes into an admin form, and saves the values into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses get_module_key()
	 * @uses get_setting()
	 * 
	 * @param array $checkboxes An array of checkboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param mixed $grouptext The text to display in a table cell to the left of the one containing the checkboxes. Optional.
	 */
	function checkboxes($checkboxes, $grouptext=false) {
		
		//Save checkbox settings after form submission
		if ($this->is_action('update')) {
			foreach ($checkboxes as $name => $desc) {
				$this->update_setting($name, $_POST[$name] == '1');
			}
		}
		
		if ($grouptext)
			echo "<tr valign='top'>\n<th scope='row'>$grouptext</th>\n<td><fieldset><legend class='hidden'>$grouptext</legend>\n";
		else
			echo "<tr valign='top'>\n<td>\n";
		
		if (is_array($checkboxes)) {
			foreach ($checkboxes as $name => $desc) {
				register_setting($this->get_module_key(), $name, 'intval');
				$name = attribute_escape($name);
				echo "<label for='$name'><input name='$name' id='$name' type='checkbox' value='1'";
				if ($this->get_setting($name) === true) echo " checked='checked'";
				echo " /> $desc</label><br />\n";
			}
		}
		
		if ($grouptext) echo "</fieldset>";
		echo "</td>\n</tr>\n";
	}
	
	/**
	 * Outputs a group of textboxes into an admin form, and saves the values into the database after form submission.
	 * Can also display a "Reset" link next to each textbox that reverts its value to a specified default.
	 * 
	 * @since 0.1
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses get_module_key()
	 * @uses get_setting()
	 * 
	 * @param array $textboxes An array of textboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param array $defaults An array of default textbox values that trigger "Reset" links. (The field/setting ID is the key, and the default value is the value.) Optional.
	 * @param mixed $grouptext The text to display in a table cell to the left of the one containing the textboxes. Optional.
	 */
	function textboxes($textboxes, $defaults=array(), $grouptext=false) {
		
		if ($this->is_action('update')) {
			foreach ($textboxes as $id => $title) {
				$this->update_setting($id, stripslashes($_POST[$id]));
			}
		}
		
		if ($grouptext)
			echo "<tr valign='top'>\n<th scope='row'>$grouptext</th>\n<td><fieldset><legend class='hidden'>$grouptext</legend>\n";
		
		foreach ($textboxes as $id => $title) {
			register_setting($this->get_module_key(), $id);
			$value = wp_specialchars($this->get_setting($id), ENT_QUOTES, false, true);
			$default = attribute_escape($defaults[$id]);
			$id = attribute_escape($id);
			$resetmessage = attribute_escape(__("Are you sure you want to replace the textbox contents with this default value?", 'seo-ultimate'));
			
			if ($grouptext)
				echo "<div class='field'><label for='$id'>$title</label><br />\n";
			else
				echo "<tr valign='top'>\n<th scope='row'><label for='$id'>$title</label></th>\n<td>";
			
			echo "<input name='$id' id='$id' type='text' value='$value' class='regular-text' ";
			if (isset($defaults[$id])) {
				echo "onkeyup=\"javascript:textbox_value_changed(this, '$default', '{$id}_reset')\" />";
				echo "&nbsp;<a href=\"javascript:void(0)\" id=\"{$id}_reset\" onclick=\"javascript:reset_textbox('$id', '$default', '$resetmessage', this)\"";
				if ($default == $value) echo ' class="hidden"';
				echo ">";
				_e('Reset', 'seo-ultimate');
				echo "</a>";
			} else {
				echo "/>";
			}
			
			if ($grouptext)
				echo "</div>\n";
			else
				echo "</td>\n</tr>\n";
		}
		
		if ($grouptext)
			echo "</td>\n</tr>\n";
	}
	
	/**
	 * Outputs a single textbox into an admin form and saves its value into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses textboxes()
	 * 
	 * @param string $id The field/setting ID.
	 * @param string $title The label of the HTML element.
	 * @return string The HTML that would render the textbox.
	 */
	function textbox($id, $title) {
		$this->textboxes(array($id => $title));
	}
	
	
	/**
	 * Outputs a group of textareas into an admin form, and saves the values into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses get_module_key()
	 * @uses get_setting()
	 * 
	 * @param array $textareas An array of textareas. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param int $rows The value of the textareas' rows attribute.
	 * @param int $cols The value of the textareas' cols attribute.
	 */
	function textareas($textareas, $rows = 5, $cols = 30) {
		
		if ($this->is_action('update')) {
			foreach ($textareas as $id => $title) {
				$this->update_setting($id, stripslashes($_POST[$id]));
			}
		}
		
		foreach ($textareas as $id => $title) {
			register_setting($this->get_module_key(), $id);
			$value = wp_specialchars($this->get_setting($id), ENT_QUOTES, false, true);
			$id = attribute_escape($id);
			
			echo "<tr valign='top'>\n<th scope='row'><label for='$id'>$title</label></th>\n";
			echo "<td><textarea name='$id' id='$id' type='text' class='regular-text' cols='$cols' rows='$rows'>$value</textarea>";
			echo "</td>\n</tr>\n";
		}
	}
	
	/**
	 * Outputs a single textarea into an admin form and saves its value into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses textareas()
	 * 
	 * @param string $id The field/setting ID.
	 * @param string $title The label of the HTML element.
	 * @param int $rows The value of the textarea's rows attribute.
	 * @param int $cols The value of the textarea's cols attribute.
	 * @return string The HTML that would render the textarea.
	 */
	function textarea($id, $title, $rows = 5, $cols = 30) {
		$this->textareas(array($id => $title), $rows, $cols);
	}
	
	/********** ADMIN SECURITY FUNCTIONS **********/
	
	/**
	 * Determines if a particular nonce-secured admin action is being executed.
	 * 
	 * @since 0.1
	 * @uses nonce_validates()
	 * 
	 * @param string $action The name of the action to check.
	 * @return bool Whether or not the action is being executed.
	 */
	function is_action($action) {
		if (!($object = $_GET['object'])) $object = false;
		return (($_GET['action'] == $action || $_POST['action'] == $action) && $this->nonce_validates($action, $object));
	}
	
	/**
	 * Determines whether a nonce is valid.
	 * 
	 * @since 0.1
	 * @uses get_nonce_handle()
	 * 
	 * @param string $action The name of the action.
	 * @param mixed $id The ID of the object being acted upon. Optional.
	 * @return bool Whether or not the nonce is valid.
	 */
	function nonce_validates($action, $id = false) {
		return check_admin_referer($this->get_nonce_handle($action, $id));
	}
	
	/**
	 * Generates a unique name for a nonce.
	 * 
	 * @since 0.1
	 * @uses get_parent_module()
	 * @uses get_module_key()
	 * @uses SU_PLUGIN_NAME
	 * 
	 * @param string $action The name of the action.
	 * @param mixed $id The ID of the object being acted upon. Optional.
	 * @return The handle to use for the nonce.
	 */
	function get_nonce_handle($action, $id = false) {
		
		$key = $this->get_parent_module();
		if (!$key) $key = $this->get_module_key();
		
		$hook = SEO_Ultimate::key_to_hook($key);
		
		if (strcmp($action, 'update') == 0) {
			//We use the settings_fields() function, which outputs a nonce in this particular format.
			return "$hook-options";
		} else {
			if ($id) $id = '-'.md5($id); else $id = '';
			$handle = SU_PLUGIN_NAME."-$hook-$action$id";
			return strtolower(str_replace(' ', '-', $handle));
		}
	}
	
	/**
	 * Returns a GET-action URL with an appended nonce.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * @uses get_nonce_handle()
	 * 
	 * @param string $action The name of the action.
	 * @param mixed $id The ID of the object being acted upon. Optional.
	 * @return The URL to use in an <a> tag.
	 */
	function get_nonce_url($action, $object=false) {
		$action = urlencode($action);
		if ($object) $objectqs = '&object='.urlencode($object); else $objectqs = '';
		
		$hook = SEO_Ultimate::key_to_hook($this->get_module_key());
		
		//We don't need to escape ampersands since wp_nonce_url will do that for us
		return wp_nonce_url("?page=$hook&action=$action$objectqs",
			$this->get_nonce_handle($action, $object));
	}
	
	
	/********** ADMIN MESSAGE FUNCTIONS **********/
	
	/**
	 * Print a message (and any previously-queued messages) right away.
	 * 
	 * @since 0.1
	 * @uses queue_message()
	 * @uses print_messages()
	 * 
	 * @param string $type The message's type. Valid values are success, error, warning, and info.
	 * @param string $message The message text.
	 */
	function print_message($type, $message) {
		$this->queue_message($type, $message);
		$this->print_messages();
	}
	
	/**
	 * Adds a message to the queue.
	 * 
	 * @since 0.1
	 * @uses $messages
	 * 
	 * @param string $type The message's type. Valid values are success, error, warning, and info.
	 * @param string $message The message text.
	 */
	function queue_message($type, $message) {
		$this->messages[$type][] = $message;
	}
	
	/**
	 * Prints all queued messages and flushes the queue.
	 * 
	 * @since 0.1
	 * @uses $messages
	 */
	function print_messages() {
		foreach ($this->messages as $type => $messages) {
			$messages = implode('<br />', $messages);
			if ($messages) {
				$type = attribute_escape($type);
				echo "<div class='su-message'><p class='su-$type'>$messages</p></div>\n";
			}
		}
		
		$this->messages = array();
	}
	
	/********** ADMIN POST META BOX FUNCTIONS **********/
	
	/**
	 * Gets a specified meta value of the current post (i.e. the post currently being edited in the admin,
	 * the post being shown, the post now in the loop, or the post with specified ID).
	 * 
	 * @since 0.1
	 * 
	 * @param string $key The meta key to fetch.
	 * @param mixed $id The ID number of the post/page.
	 * @return string The meta value requested.
	 */
	function get_postmeta($key, $id=false) {

		if (!$id) {
			if (is_admin())
				$id = intval($_REQUEST['post']);
			elseif (in_the_loop())
				$id = intval(get_the_ID());
			elseif (is_singular()) {
				global $wp_query;
				$id = $wp_query->get_queried_object_id();
			}
		}
		
		if ($id) return get_post_meta($id, "_su_$key", true);
		
		return '';
	}

	/**
	 * Generates the HTML for multiple post meta textboxes.
	 * 
	 * @since 0.1
	 * @uses get_postmeta()
	 * 
	 * @param array $textboxes An array of textboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @return string The HTML that would render the textboxes.
	 */
	function get_postmeta_textboxes($textboxes) {

		$html = '';
		
		foreach ($textboxes as $id => $title) {
		
			register_setting('seo-ultimate', $id);
			$value = attribute_escape($this->get_postmeta($id));
			$id = "_su_".attribute_escape($id);
			$title = str_replace(' ', '&nbsp;', $title);
			
			$html .= "<tr class='textbox'>\n<th scope='row'><label for='$id'>$title</label></th>\n"
					."<td><input name='$id' id='$id' type='text' value='$value' class='regular-text' /></td>\n</tr>\n";
		}
		
		return $html;
	}

	/**
	 * Generates the HTML for a single post meta textbox.
	 * 
	 * @since 0.1
	 * @uses get_postmeta_textboxes()
	 * 
	 * @param string $id The ID of the HTML element.
	 * @param string $title The label of the HTML element.
	 * @return string The HTML that would render the textbox.
	 */
	function get_postmeta_textbox($id, $title) {
		return $this->get_postmeta_textboxes(array($id => $title));
	}
	
	/**
	 * Generates the HTML for a group of post meta checkboxes.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * @uses get_postmeta()
	 * 
	 * @param array $checkboxes An array of checkboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param string $grouptext The text to display in a table cell to the left of the one containing the checkboxes.
	 */
	function get_postmeta_checkboxes($checkboxes, $grouptext) {
		
		$html = "<tr>\n<th scope='row'>$grouptext</th>\n<td><fieldset><legend class='hidden'>$grouptext</legend>\n";
		
		if (is_array($checkboxes)) {
			foreach ($checkboxes as $name => $desc) {
				
				register_setting('seo-ultimate', $name);
				$checked = ($this->get_postmeta($name) == 1);
				$name = "_su_".attribute_escape($name);
				
				$html .= "<label for='$name'><input name='$name' id='$name' type='checkbox' value='1'";
				if ($checked) $html .= " checked='checked'";
				$html .= " /> $desc</label><br />\n";
			}
		}
		
		$html .= "</fieldset></td>\n</tr>\n";
		
		return $html;
	}
	
	/**
	 * Generates the HTML for a single post meta checkbox.
	 * 
	 * @since 0.1
	 * @uses get_postmeta_checkboxes()
	 * 
	 * @param string $id The ID of the HTML element.
	 * @param string $title The label of the HTML element.
	 * @param string $grouptext The text to display in a table cell to the left of the one containing the checkboxes.
	 * @return string The HTML that would render the textbox.
	 */
	function get_postmeta_checkbox($id, $title, $grouptext) {
		return $this->get_postmeta_checkboxes(array($id => $title), $grouptext);
	}
	
	
	/********** CRON FUNCTION **********/
	
	/**
	 * Creates a cron job if it doesn't already exists, and ensures it runs at the scheduled time.
	 * Should be called in a module's init() function.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * 
	 * @param string $function The name of the module function that should be run.
	 * @param string $recurrance How often the job should be run. Valid values are hourly, twicedaily, and daily.
	 */
	function cron($function, $recurrance) {
		
		$mk = $this->get_module_key();
		
		$hook = "su-$mk-".str_replace('_', '-', $function);
		$start = time();
		
		if (wp_next_scheduled($hook) === false) {
			//This is a new cron job
			
			//Schedule the event
			wp_schedule_event($start, $recurrance, $hook);
			
			//Make a record of it
			$data = maybe_unserialize(get_option('su_cron'));
			if (!is_array($data)) $data = array();
			$data[$mk][$function] = array($hook, $start, $recurrance);
			update_option('su_cron', serialize($data));
			
			//Run the event now
			call_user_func(array($this, $function));
		}
		
		add_action($hook, array($this, $function));
	}
	
	/********** RSS FUNCTION **********/
	
	/**
	 * Loads an RSS feed and returns it as an object.
	 * 
	 * @since 0.1
	 * @uses get_user_agent() Hooks into WordPress's http_header_useragent filter.
	 * 
	 * @param string $url The URL of the RSS feed to load.
	 * @return object $rss The RSS object.
	 */
	function load_rss($url) {
		add_filter('http_headers_useragent', 'su_get_user_agent');
		require_once (ABSPATH . WPINC . '/rss.php');
		$rss = fetch_rss($url);
		remove_filter('http_headers_useragent', 'su_get_user_agent');
		return $rss;
	}
}
?>