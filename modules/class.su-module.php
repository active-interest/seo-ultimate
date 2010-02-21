<?php
/**
 * The pseudo-abstract class upon which all modules are based.
 * 
 * @abstract
 * @since 0.1
 */
class SU_Module {
	
	/********** VARIABLES **********/
	
	/**
	 * @since 0.1
	 * @var string
	 */
	var $module_key;
	
	/**
	 * Stores the parent module if applicable.
	 * 
	 * @since 1.5
	 * @var SU_Module
	 */
	var $parent_module = null;
	
	/**
	 * Stores any child modules.
	 * 
	 * @since 1.5
	 * @var array
	 */
	var $modules = array();
	
	/**
	 * Stores the module file's URL.
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $module_url;
	
	/**
	 * Stores the URL to the directory containing the module file's URL. Has trailing slash.
	 * 
	 * @since 1.5
	 * @var string
	 */
	var $module_dir_url;
	
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
	
	/**
	 * Stores the plugin object by reference.
	 * 
	 * @since 1.5
	 */
	var $plugin = null;
	
	
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
	 * The module's official title.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_module_title() { return ''; }
	
	/**
	 * The title to be used by parent modules.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_module_subtitle() { return $this->get_module_title(); }
	
	/**
	 * The title of the admin page, which is displayed in the <title> and <h2> tags.
	 * Is the same as the menu title by default.
	 * 
	 * @since 0.1
	 * 
	 * @return string The title shown on this module's admin page.
	 */
	function get_page_title() { return $this->get_module_title(); }
	
	/**
	 * The title that appears on the administration navigation menu.
	 * 
	 * @since 0.1
	 * 
	 * @return string The title shown on the admin menu.
	 */
	function get_menu_title() { return $this->get_module_title(); }
	
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
	 * Determines where this module's admin contents should appear on the parent page relative to those of other sibling modules.
	 * If two modules have the same order index, they are sorted alphabetically.
	 * 
	 * @since 1.5
	 * 
	 * @return int The child order index.
	 */
	function get_child_order() { return 999; }
	
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
	 * Whether or not the module will ever return a non-zero menu count.
	 * 
	 * @since 1.5
	 * 
	 * @return boolean
	 */
	function has_menu_count() { return false; }
	
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
	 * The status (enabled/silenced/hidden) of the module when the module is newly added to the plugin.
	 * 
	 * @since 1.5
	 * 
	 * @return int Either SU_MODULE_ENABLED, SU_MODULE_SILENCED, or SU_MODULE_HIDDEN.
	 */
	function get_default_status() { return SU_MODULE_ENABLED; }
	
	/**
	 * The module key of this module's parent. Defaults to false (no parent).
	 * 
	 * @since 0.3
	 * 
	 * @return string|bool
	 */
	function get_parent_module() { return false; }
	
	/**
	 * Returns an array of admin page tabs; the label is the key and the callback is the value.
	 * 
	 * @since 1.5
	 * 
	 * @return array
	 */
	function get_admin_page_tabs() { return array(); }
	
	/**
	 * Whether or not the module can "exist on its own."
	 * Determines whether or not the module appears in the Module Manager.
	 * 
	 * @since 1.5
	 * 
	 * @return bool
	 */
	function is_independent_module() {
		return true;
	}
	
	/**
	 * The array key of the plugin's settings array in which this module's settings are stored.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_settings_key() {
		if (strlen($parent = $this->get_parent_module()) && !$this->is_independent_module())
			return $this->plugin->modules[$parent]->get_settings_key();
		else
			return $this->get_module_key();
	}
	
	/**
	 * Whether or not this module should be the default screen for the "SEO" menu.
	 * 
	 * @since 1.5
	 * @return bool
	 */
	function is_menu_default() { return false; }
	
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
	function admin_page_contents() {
		$this->children_admin_page_tabs_form();
	}
	
	/**
	 * Returns an array of custom contextual help dropdowns; internationalized titles are the array keys and contents are the array values.
	 * 
	 * @since 1.5
	 * @uses sumd::get_sections()
	 * @uses sumd::get_section()
	 * @uses SEO_Ultimate::get_translated_readme_path()
	 * @uses SEO_Ultimate::get_readme_path()
	 * 
	 * @return array
	 */
	function get_admin_dropdowns() {
		
		$paths = array($this->plugin->get_translated_readme_path(), $this->plugin->get_readme_path());
		
		foreach ($paths as $path) {
			$readme = file_get_contents($path);
			$sections = sumd::get_sections(sumd::get_section($readme, $this->get_module_title()));
			if (count($sections)) {
				
				if (sustr::has($path, '/translations/') && preg_match("|\nStable tag: ([a-zA-Z0-9. ]+)|i", $readme, $matches)) {
					$version = $matches[1];
					if (version_compare($version, SU_VERSION, '<'))
						$sections = suarr::aprintf(false, '%s<p><em>'
							. __('(Note: This translated documentation was designed for an older version of SEO Ultimate and may be outdated.)', 'seo-ultimate')
							. '</em></p>'
						, $sections);
				}
				
				return $sections;
			}
		}
		
		return array();
	}
	
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
	 * Returns the absolute URL of the module's admin page.
	 * 
	 * @since 0.7
	 * @uses get_parent_module()
	 * @uses get_module_key()
	 * @uses SEO_Ultimate::key_to_hook()
	 * 
	 * @param string|false $key The key of the module for which to generate the admin URL. Optional.
	 * @return string The absolute URL to the admin page.
	 */
	function get_admin_url($key = false) {
		
		if ($key === false) {
			if ($key = $this->get_parent_module()) {
				$anchor = '#'.$this->plugin->key_to_hook($this->get_module_key());
			} else {
				$key = $this->get_module_key();
				$anchor = '';
			}
		}
		
		$basepage = 'admin.php';
		if (isset($this->plugin->modules[$key]) && sustr::endswith($custom_basepage = $this->plugin->modules[$key]->get_menu_parent(), '.php'))
			$basepage = $custom_basepage;
		
		return admin_url($basepage.'?page='.$this->plugin->key_to_hook($key).$anchor);
	}
	
	/**
	 * Returns an <a> link to the module's admin page, if the module is enabled.
	 * 
	 * @since 1.0
	 * @uses get_admin_url()
	 * 
	 * @param string|false $key The key of the module for which to generate the admin URL.
	 * @param string $label The text to go inside the <a> element.
	 * @return string The <a> element, if the module exists; otherwise, the label by itself.
	 */
	function get_admin_link($key, $label) {
	
		if ($key == false || $this->plugin->module_exists($key))
			return sprintf('<a href="%s">%s</a>', $this->get_admin_url($key), $label);
		else
			return $label;
	}
	
	/**
	 * Returns a boolean indicating whether the user is currently viewing this module's admin page.
	 * 
	 * @since 1.1.1
	 * 
	 * @return bool Whether the user is currently viewing this module's admin page.
	 */
	function is_module_admin_page() {
		if (is_admin()) {
			global $plugin_page;
			if (strcmp($plugin_page, $this->plugin->key_to_hook($this->get_module_key())) == 0) return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the filename of the module's icon URL.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_menu_icon_filename() {
		$filenames = array(
			  $this->get_settings_key()
			, $this->get_module_key()
			, $this->get_parent_module()
		);
		
		foreach ($filenames as $filename) {
			$image = $this->module_dir_url.$filename.'.png';
			if (is_readable($image)) return $image;
		}
		
		return '';
	}
	
	
	/********** CHILD MODULE FUNCTIONS **********/
	
	/**
	 * Finds child modules of this module and fills $this->modules accordingly.
	 * 
	 * @since 1.5
	 */
	function load_child_modules() {
		foreach ($this->plugin->modules as $key => $x_module) {
			if ($key != $this->get_module_key()) {
				$module =& $this->plugin->modules[$key];
				if ($module->get_parent_module() == $this->get_module_key()) {
					$module->parent_module =& $this;
					$this->modules[$key] =& $module;
				}
			}
		}
		
		if (count($this->modules) > 0)
			uasort($this->modules, array(&$this, 'module_sort_callback'));
	}
	
	/**
	 * Returns an array of this module's admin tabs plus those of its children.
	 * 
	 * @since 1.5
	 * @return array
	 */
	function get_children_admin_page_tabs() {
		$tabs = $this->get_admin_page_tabs();
		
		foreach ($this->modules as $key => $x_module) {
			$module =& $this->modules[$key];
			$child_tabs = $module->get_admin_page_tabs();
			if (empty($child_tabs))
				$child_tabs[$module->get_module_subtitle()] = array(&$module, 'admin_page_contents');
			$tabs = array_merge($tabs, $child_tabs);
		}
		
		return $tabs;
	}
	
	/**
	 * Outputs this module's admin tabs plus those of its children.
	 * 
	 * @since 1.5
	 * @return array
	 */
	function children_admin_page_tabs() {
		if (count($tabs = $this->get_children_admin_page_tabs()))
			$this->admin_page_tabs($tabs);
	}
	
	/**
	 * Outputs a form containing this module's admin tabs plus those of its children.
	 * 
	 * @since 1.5
	 */
	function children_admin_page_tabs_form() {
		if (count($tabs = $this->get_children_admin_page_tabs())) {
			$this->admin_form_start(false, false);
			$this->admin_page_tabs($tabs);
			$this->admin_form_end(false, false);
		}
	}
	
	/**
	 * Outputs the admin pages of this module's children, one after the other.
	 * 
	 * @since 1.5
	 */
	function children_admin_pages() {
		foreach ($this->modules as $key => $x_module) {
			$this->modules[$key]->admin_page_contents();
		}
	}
	
	/**
	 * Outputs a form containing the admin pages of this module's children, outputted one after the other.
	 * 
	 * @since 1.5
	 */
	function children_admin_pages_form() {
		$this->admin_form_start();
		$this->children_admin_pages();
		$this->admin_form_end();
	}
	
	/**
	 * Compares two modules to determine which of the two should be displayed first on the parent page.
	 * Sorts by child order first, and title second.
	 * Works as a uasort() callback.
	 * 
	 * @since 1.5
	 * @uses SU_Module::get_child_order()
	 * @uses SU_Module::get_module_subtitle()
	 * 
	 * @param SU_Module $a The first module to compare.
	 * @param SU_Module $b The second module to compare.
	 * @return int This will be -1 if $a comes first, or 1 if $b comes first.
	 */
	function module_sort_callback($a, $b) {
		
		if ($a->get_child_order() == $b->get_child_order()) {
			return strcmp($a->get_module_subtitle(), $b->get_module_subtitle());
		}
		
		return ($a->get_child_order() < $b->get_child_order()) ? -1 : 1;
	}
	
	/********** SETTINGS FUNCTIONS **********/
	
	/**
	 * Retrieves the given setting from a module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_settings_key()
	 * 
	 * @param string $key The name of the setting to retrieve.
	 * @param mixed $default What should be returned if the setting does not exist. Optional.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module's settings key. Optional.
	 * @return mixed The value of the setting, or the $default variable.
	 */
	function get_setting($key, $default=null, $module=null) {
		if (!$module) $module = $this->get_settings_key();
		
		if (isset($this->plugin->dbdata['settings'][$module][$key]))
			$setting = $this->plugin->dbdata['settings'][$module][$key];
		else
			$setting = $default;
		
		$setting = apply_filters("su_get_setting-$module", $setting, $key);
		$setting = apply_filters("su_get_setting-$module-$key", $setting, $key);
		
		return $setting;
	}
	
	/**
	 * Sets a value in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_settings_key()
	 * 
	 * @param string $key The key of the setting to be changed.
	 * @param string $value The new value to assign to the setting.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module's settings key. Optional.
	 */
	function update_setting($key, $value, $module=null) {
		if (!$module) $module = $this->get_settings_key();
		
		$use_custom  = 	apply_filters("su_custom_update_setting-$module-$key", false, $value, $key) ||
						apply_filters("su_custom_update_setting-$module", false, $value, $key);
		
		if (!$use_custom)
			$this->plugin->dbdata['settings'][$module][$key] = $value;
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
	
	/**
	 * Returns a default setting. Only use this function if a default is indeed provided!
	 * 
	 * @since 1.3
	 * @uses get_default_settings()
	 * 
	 * @param string $key The name of the setting whose default to retrieve.
	 * @return mixed The default value for the setting.
	 */
	function get_default_setting($key) {
		$defaults = $this->get_default_settings();
		return $defaults[$key];
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
	 * @uses screen_meta_filter() Hooked into our screen_meta filter
	 * @uses get_module_key()
	 * @uses get_page_title()
	 * 
	 * @param string $icon The ID that should be applied to the icon element. The icon is loaded via CSS based on the ID. Optional.
	 */
	function admin_page_start($icon = 'options-general') {
		
		//Add our custom footer attribution
		add_action('in_admin_footer', array(&$this, 'admin_footer'));
		
		//Add our custom contextual help
		add_filter('screen_meta', array(&$this, 'screen_meta_filter'));
		
		//Output the beginning of the admin screen
		echo "<div class=\"wrap\">\n";
		
		if (strcmp($pclass = strtolower(get_parent_class($this)), 'su_module') != 0)
			$class = ' '.str_replace('_', '-', $pclass);
		else
			$class = '';
		
		echo "<div id=\"su-".su_esc_attr($this->get_module_key())."\" class=\"su-module$class\">\n";
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
	 * @uses SEO_Ultimate::plugin_dir_url
	 * 
	 * @param array $tabs The internationalized tab titles are the array keys, and the references to the functions that display the tab contents are the array values.
	 * @param bool $table Whether or not the tab contents should be wrapped in a form table.
	 */
	function admin_page_tabs($tabs = array(), $table=false) {
		
		if ($c = count($tabs)) {
			
			if ($c > 1)
				echo "\n\n<div id='su-tabset' class='su-tabs'>\n";
			
			foreach ($tabs as $title => $function) {
				
				if ($c > 1) {
					$id = preg_replace('/[^a-z0-9]/', '', strtolower($title));
					echo "<fieldset id='$id'>\n<h3>$title</h3>\n";
				}
				
				if ($table) echo "<table class='form-table'>\n";
				
				$call = $args = array();
				
				if (is_array($function)) {
					
					if (is_array($function[0])) {
						$call = array_shift($function);
						$args = $function;
					} elseif (is_string($function[0])) {
						$call = array_shift($function);
						$call = array(&$this, $call);
						$args = $function;
					} else {
						$call = $function;
					}
				} else {
					$call = array(&$this, $function);
				}
				if (is_callable($call)) call_user_func_array($call, $args);
				
				if ($table) echo "</table>";
				
				if ($c > 1)
					echo "</fieldset>\n";
			}
			
			if ($c > 1) {
				echo "</div>\n";
		
				echo '<script type="text/javascript" src="'.$this->plugin->plugin_dir_url.'includes/tabs.js?v='.SU_VERSION.'"></script>';
			}
		}
	}
	
	/**
	 * Adds the hook necessary to initialize the admin page tabs.
	 * 
	 * @since 0.8
	 */
	function admin_page_tabs_init() {
		add_action('admin_print_scripts', array(&$this, 'admin_page_tabs_js'));
	}
	
	/**
	 * Enqueues the JavaScript needed for the admin page tabs.
	 * 
	 * @since 0.8
	 * @uses is_module_admin_page()
	 */
	function admin_page_tabs_js() {
		if ($this->is_module_admin_page())
			wp_enqueue_script('jquery-ui-tabs');
	}
	
	/**
	 * Adds the module's custom screen meta, if present.
	 * 
	 * @since 0.9
	 * @uses admin_dropdowns()
	 */
	function screen_meta_filter($screen_meta) {
		
		$sections = array_reverse($this->get_admin_dropdowns());
		
		if (is_array($sections) && count($sections)) {
			foreach ($sections as $label => $text) {
				$key = preg_replace('|[^a-z]|', '', strtolower($label));
				$label = htmlspecialchars($label);
				$content  = "<div class='su-help'>\n";
				
				$header = sprintf(_c('%s %s|Dropdown Title', 'seo-ultimate'), $this->get_module_title(), $label);
				$header = sustr::unique_words($header);
				
				$text = wptexturize(Markdown($text));
				$text = str_replace('<a ', '<a target="_blank" ', $text);
				
				$content .= "<h5>$header</h5>\n\n";
				$content .= $text;
				$content .= "\n</div>\n";
				$screen_meta[] = compact('key', 'label', 'content');
			}
			
			echo "<script type='text/javascript'>jQuery(function($) { $('#contextual-help-link').css('display', 'none'); });</script>";
		}
		
		return $screen_meta;
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
			$this->get_module_title(),
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
	 * @uses print_message()
	 * @uses get_parent_module()
	 * 
	 * @param mixed $header The text of the subheader that should go right before the form. Optional.
	 * @param boolean $table Whether or not to start a form table.
	 */
	function admin_form_start($header = false, $table = true) {
		$hook = $this->plugin->key_to_hook($this->get_module_key());
		if ($header) $this->admin_subheader($header);
		
		if (!$this->get_parent_module()) {
			if ($this->is_action('update')) $this->print_message('success', __('Settings updated.', 'seo-ultimate'));
			echo "<form id='su-admin-form' method='post' action='?page=$hook'>\n";
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
	 * Begins an admin form table.
	 * 
	 * @since 1.5
	 */
	function admin_form_table_start() {
		echo "<table class='form-table'>\n";
	}
	
	/**
	 * Ends an admin form table
	 * 
	 * @since 1.5
	 */
	function admin_form_table_end() {
		echo "</table>\n";
	}
	
	/**
	 * Outputs the HTML that begins an admin form group.
	 * 
	 * @since 1.5
	 * 
	 * @param string $title The title of the group.
	 * @param bool $newtable Whether to open a new <table> element.
	 */
	function admin_form_group_start($title, $newtable=true) {
		echo "<tr valign='top'>\n<th scope='row'>$title</th>\n<td><fieldset><legend class='hidden'>$title</legend>\n";
		if ($newtable) echo "<table>\n";
	}
	
	/**
	 * Outputs the HTML that ends an admin form group.
	 * 
	 * @since 1.5
	 * 
	 * @param bool $newtable Whether to close a <table> element.
	 */
	function admin_form_group_end($newtable=true) {
		if ($newtable) echo "</table>\n";
		echo "</td>\n</tr>\n";
	}
	
	function admin_form_indent_start() {
		echo "<tr valign='top'><td colspan='2'><table class='su-indent'>";
	}
	
	function admin_form_indent_end() {
		echo "</table></td></tr>";
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
	 * Outputs a text block into an admin form.
	 * 
	 * @since 1.5
	 * 
	 * @param string $text
	 */
	function textblock($text) {
		echo "<tr valign='top' class='su-admin-form-textblock'>\n<td colspan='2'>\n";
		echo $text;
		echo "\n</td>\n</tr>\n";
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
				
				if (strpos($desc, '%d') !== false) {
					$name .= '_value';
					$this->update_setting($name, intval($_POST[$name]));
				}
			}
		}
		
		if ($grouptext)
			$this->admin_form_group_start($grouptext, false);
		else
			echo "<tr valign='top' class='su-admin-form-checkbox'>\n<td colspan='2'>\n";
		
		if (is_array($checkboxes)) {
			foreach ($checkboxes as $name => $desc) {
				
				//$desc = preg_replace_callback('/%d/', array(&$this, "insert_int_var_textboxes"), $desc);
				
				register_setting($this->get_module_key(), $name, 'intval');
				$name = su_esc_attr($name);
				
				if (strpos($desc, '%d') === false) {
					$onclick = '';
				} else {
					$int_var_name = $name.'_value';
					$int_var_value = intval($this->get_setting($int_var_name));
					if ($this->get_setting($name) === true) $disabled = ''; else $disabled = "readonly='readonly' ";
					$desc = str_replace('%d', "</label><input name='$int_var_name' id='$int_var_name' type='text' value='$int_var_value' size='2' maxlength='3' $disabled/><label for='$name'>", $desc);
					$desc = str_replace("<label for='$name'></label>", '', $desc);
					$onclick = " onclick=\"javascript:document.getElementById('$int_var_name').readOnly=!this.checked;\"";
				}
				
				echo "<label for='$name'><input name='$name' id='$name' type='checkbox' value='1'";
				if ($this->get_setting($name) === true) echo " checked='checked'";
				echo "$onclick /> $desc</label><br />\n";
			}
		}
		
		if ($grouptext) echo "</fieldset>";
		echo "</td>\n</tr>\n";
	}
	
	/**
	 * Outputs a single checkbox into an admin form and saves its value into the database after form submission.
	 * 
	 * @since 1.5
	 * @uses checkboxes()
	 * 
	 * @param string $id The field/setting ID.
	 * @param string $desc The checkbox's label.
	 * @return string The HTML that would render the checkbox.
	 */
	function checkbox($id, $desc) {
		$this->checkboxes(array($id => $desc));
	}
	
	/**
	 * Outputs a set of radio buttons into an admin form and saves the set's value into the database after form submission.
	 * 
	 * @since 1.5
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses admin_form_group_start()
	 * @uses admin_form_group_end()
	 * @uses su_esc_attr()
	 * @uses get_setting()
	 * 
	 * @param string $name The name of the set of radio buttons.
	 * @param array $values The keys of this array are the radio button values, and the array values are the label strings.
	 * @param string|false $grouptext The text to display in a table cell to the left of the one containing the radio buttons. Optional.
	 */
	function radiobuttons($name, $values, $grouptext=false) {
		
		//Save radio button setting after form submission
		if ($this->is_action('update'))
			$this->update_setting($name, $_POST[$name]);
		
		if ($grouptext)
			$this->admin_form_group_start($grouptext, false);
		else
			echo "<tr valign='top' class='su-admin-form-radio'>\n<td colspan='2'>\n";
		
		if (is_array($values)) {
			
			register_setting($this->get_module_key(), $name);
			$name = su_esc_attr($name);
			
			foreach ($values as $value => $desc) {
				
				$value = su_esc_attr($value);
				$id = "{$name}_{$value}";
				
				echo "<label for='$id'><input name='$name' id='$id' type='radio' value='$value'";
				if (strcmp($this->get_setting($name), $value) == 0) echo " checked='checked'";
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
		
		if ($grouptext) $this->admin_form_group_start($grouptext, false);
		
		foreach ($textboxes as $id => $title) {
			register_setting($this->get_module_key(), $id);
			$value = su_esc_editable_html($this->get_setting($id));
			$default = su_esc_editable_html($defaults[$id]);
			$id = su_esc_attr($id);
			$resetmessage = su_esc_attr(__("Are you sure you want to replace the textbox contents with this default value?", 'seo-ultimate'));
			
			if ($grouptext)
				echo "<div class='field'><label for='$id'>$title</label><br />\n";
			elseif (strpos($title, '</a>') === false)
				echo "<tr valign='top'>\n<th scope='row'><label for='$id'>$title</label></th>\n<td>";
			else
				echo "<tr valign='top'>\n<td>$title</td>\n<td>";
			
			echo "<input name='$id' id='$id' type='text' value='$value' class='regular-text' ";
			if (isset($defaults[$id])) {
				echo "onkeyup=\"javascript:su_textbox_value_changed(this, '$default', '{$id}_reset')\" />";
				echo "&nbsp;<a href=\"javascript:void(0)\" id=\"{$id}_reset\" onclick=\"javascript:su_reset_textbox('$id', '$default', '$resetmessage', this)\"";
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
		
		if ($grouptext) $this->admin_form_group_end(false);
	}
	
	/**
	 * Outputs a single textbox into an admin form and saves its value into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses textboxes()
	 * 
	 * @param string $id The field/setting ID.
	 * @param string $title The label of the HTML element.
	 * @param string|false $default The default textbox value. Setting this will trigger a "Reset" link. Optional.
	 * @return string The HTML that would render the textbox.
	 */
	function textbox($id, $title, $default=false) {
		if ($default === false) $default = array(); else $default = array($id => $default);
		$this->textboxes(array($id => $title), $default);
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
			$value = su_esc_editable_html($this->get_setting($id));
			$id = su_esc_attr($id);
			
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
	 * @uses SEO_Ultimate::key_to_hook()
	 * @uses get_module_key()
	 * @uses nonce_validates()	 
	 * 
	 * @param string $action The name of the action to check.
	 * @return bool Whether or not the action is being executed.
	 */
	function is_action($action) {
		if (!($object = $_GET['object'])) $object = false;
		return (
					(
						   ( strcasecmp($_GET['page'], $this->plugin->key_to_hook($this->get_module_key())) == 0 ) //Is $this module being shown?
						|| ( strlen($this->get_parent_module()) && strcasecmp($_GET['page'], $this->plugin->key_to_hook($this->get_parent_module())) == 0)
					)
					&& ($_GET['action'] == $action || $_POST['action'] == $action) //Is this $action being executed?
					&& $this->nonce_validates($action, $object) //Is the nonce valid?
		);
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
		
		$hook = $this->plugin->key_to_hook($key);
		
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
		
		$hook = $this->plugin->key_to_hook($this->get_module_key());
		
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
				$type = su_esc_attr($type);
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
			//This code is different from suwp::get_post_id();
			if (is_admin()) {
				$id = intval($_REQUEST['post']);
				global $post;
			} elseif (in_the_loop()) {
				$id = intval(get_the_ID());
				global $post;
			} elseif (is_singular()) {
				global $wp_query;
				$id = $wp_query->get_queried_object_id();
				$post = $wp_query->get_queried_object();
			}
		}
		
		if ($id)
			$value = get_post_meta($id, "_su_$key", true);
		else
			$value = '';
		
		$value = apply_filters("su_get_postmeta", $value, $key, $post);
		$value = apply_filters("su_get_postmeta-$key", $value, $key, $post);
		
		return $value;
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
			$value = su_esc_editable_html($this->get_postmeta($id));
			$id = "_su_".su_esc_attr($id);
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
				$name = "_su_".su_esc_attr($name);
				
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
			$this->plugin->dbdata['cron'][$mk][$function] = array($hook, $start, $recurrance);
			
			//Run the event now
			call_user_func(array($this, $function));
		}
		
		add_action($hook, array(&$this, $function));
	}
}
?>