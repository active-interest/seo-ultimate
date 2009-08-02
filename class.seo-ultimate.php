<?php
/**
 * The main class. Provides plugin-level functionality.
 * 
 * @since 0.1
 */
class SEO_Ultimate {
	
	//Note: Throughout this class and the rest of the plugin, you may notice the occasional code segment that is apparently unused by any of this plugin's modules.
	//The core SEO Ultimate classes form a module development framework; for compatibility reasons, I develop both existing modules and upcoming modules off of the same framework.
	//This is why you may see new code in the SEO Ultimate framework some time before I bundle modules that use it.
	
	/********** VARIABLES **********/
	
	/**
	 * Stores all module class instances.
	 * 
	 * @since 0.1
	 * @var array
	 */
	var $modules = array();
	
	/**
	 * Stores the names of disabled modules.
	 * 
	 * @since 0.1
	 * @var array
	 */
	var $disabled_modules = array();
	
	/**
	 * Stores all database data.
	 * 
	 * @since 0.8
	 * @var array
	 */
	var $dbdata = array();
	
	/**
	 * The server path of the main plugin file.
	 * Example: /home/user/public_html/wp-content/plugins/seo-ultimate/seo-ultimate.php
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $plugin_file_path;
	
	/**
	 * The public URL of the main plugin file.
	 * Example: http://www.example.com/wp-content/plugins/seo-ultimate/seo-ultimate.php
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $plugin_file_url;
	
	/**
	 * The server path of the directory where this plugin is located, with trailing slash.
	 * Example: /home/user/public_html/wp-content/plugins/seo-ultimate/
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $plugin_dir_path;
	
	/**
	 * The public URL of the directory where this plugin is located, with trailing slash.
	 * Example: http://www.example.com/wp-content/plugins/seo-ultimate/
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $plugin_dir_url;
	
	/**
	 * The array to be inserted into the hits table.
	 * 
	 * @since 0.9
	 * @var array
	 */
	var $hit = array();
	
	/**
	 * The name of the function/mechanism that triggered the current redirect.
	 * 
	 * @since 0.3
	 * @var string
	 */
	var $hit_redirect_trigger = '';
	
	
	/********** CLASS CONSTRUCTORS **********/
	
	/**
	 * Fills in class variables, loads modules, and hooks into WordPress.
	 * PHP5-style constructor.
	 * 
	 * @since 0.1
	 * @uses $dbdata
	 * @uses save_dbdata() Hooked into WordPress's "shutdown" action.
	 * @uses save_hit() Hooked into WordPress's "shutdown" action.
	 * @uses load_plugin_data()
	 * @uses SU_VERSION
	 * @uses install()
	 * @uses upgrade()
	 * @uses load_modules()
	 * @uses activate() Registered with WordPress as the activation hook.
	 * @uses init() Hooked into WordPress's "init" action.
	 * @uses add_menus() Hooked into WordPress's "admin_menu" action.
	 * @uses admin_includes() Hooked into WordPress's "admin_head" action.
	 * @uses plugin_page_notices() Hooked into WordPress's "admin_head" action.
	 * @uses admin_help() Hooked into WordPress's "contextual_help" filter.
	 * @uses add_postmeta_box() Hooked into WordPress's "admin_menu" action.
	 * @uses save_postmeta_box() Hooked into WordPress's "save_post" action.
	 * @uses plugin_update_info() Hooked into the "in_plugin_update_message-seo-ultimate/seo-ultimate.php" action.
	 * @uses log_redirect_canonical() Hooked into WordPress's "redirect_canonical" filter.
	 * @uses log_redirect() Hooked into WordPress's "wp_redirect" filter.
	 * @uses log_hit() Hooked into WordPress's "status_header" filter.
	 */
	function __construct() {
		
		/********** LOAD/SAVE DATABASE DATA **********/
		
		//Load
		$this->dbdata = get_option('seo_ultimate', array());
		$this->upgrade_to_08();
		
		//Save
		add_action('shutdown', array($this, 'save_dbdata'));
		add_action('shutdown', array($this, 'save_hit'));
		
		/********** CLASS CONSTRUCTION **********/
		
		//Load data about the plugin file itself into the class
		$this->load_plugin_data();
		
		
		/********** VERSION CHECKING **********/
		
		//Get the current version
		$version = SU_VERSION;
		
		//Get the version when the plugin last ran.
		if (isset($this->dbdata['version']))
			$oldversion = $this->dbdata['version'];
		else
			$oldversion = get_option('su_version', false);

		//Or, if this is the first time the plugin is running, then install()			
		if ($oldversion) {
			
			//If $oldversion is less than $version, then upgrade()
			if (version_compare($version, $oldversion) == 1)
				$this->upgrade($oldversion);
		} else {
			$this->install();
		}
		
		//Store the current version in the database.
		$this->dbdata['version'] = $version;
		
		
		/********** INITIALIZATION **********/
		
		//Load plugin modules. Must be called *after* load_plugin_data()
		$this->load_modules();
		
		
		/********** PLUGIN EVENT HOOKS **********/
		
		//If we're activating the plugin, then call the activation function
		register_activation_hook($this->plugin_file_path, array($this, 'activate'));
		
		//If we're deactivating the plugin, then call the deactivation function
		register_deactivation_hook($this->plugin_file_path, array($this, 'deactivate'));
		
		//If we're uninstalling the plugin, then call the uninstallation function
		register_uninstall_hook($this->plugin_file_path, 'su_uninstall');
		
		
		/********** ACTION & FILTER HOOKS **********/
		
		//Initializes modules at WordPress initialization
		add_action('init', array($this, 'init'));
		
		//Hook to output all <head> code
		add_action('wp_head', array($this, 'template_head'), 1);
		
		//Hook to include JavaScript and CSS
		add_action('admin_head', array($this, 'admin_includes'));
		
		//Hook to add plugin notice actions
		add_action('admin_head', array($this, 'plugin_page_notices'));
		
		//When loading the admin menu, call on our menu constructor function.
		//For future-proofing purposes, we specifically state the default priority of 10,
		//since some modules set a priority of 9 with the specific intention of running
		//before this main plugin's hook.
		add_action('admin_menu', array($this, 'add_menus'), 10);
		
		//Hook to customize contextual help
		add_filter('contextual_help', array($this, 'admin_help'), 10, 2);
		
		//Postmeta box hooks
		add_action('admin_menu', array($this, 'add_postmeta_box'));
		add_action('save_post',  array($this, 'save_postmeta_box'), 10, 2);
		
		//Display info on new versions
		add_action('in_plugin_update_message-'.plugin_basename($this->plugin_file_path), array($this, 'plugin_update_info'), 10, 2);
		
		//Log this visitor!
		add_filter('redirect_canonical', array($this, 'log_redirect_canonical'));
		add_filter('wp_redirect', array($this, 'log_redirect'), 10, 2);
		add_filter('status_header', array($this, 'log_hit'), 10, 2);
	}
	
	/**
	 * PHP4 constructor that redirects to the PHP5 constructor.
	 * 
	 * @since 0.1
	 * @uses __construct()
	 */
	function SEO_Ultimate() {
	
		$this->__construct();
	}
	
	
	/********** PLUGIN EVENT FUNCTIONS **********/
	
	/**
	 * This will be called if the plugin is being run for the first time.
	 * 
	 * @since 0.1
	 */
	function install() {
		
		//Add the database table
		$this->db_setup();
		
		//Load settings file if present
		if (!isset($this->dbdata['settings']) && is_readable($settingsfile = $this->plugin_dir_path.'settings.txt')) {
			$import = base64_decode(file_get_contents($settingsfile));
			if (is_serialized($import)) $this->dbdata['settings'] = unserialize($import);
		}
	}
	
	/**
	 * This will be called if the plugin's version has increased since the last run.
	 * 
	 * @since 0.1
	 * 
	 * @param string $oldversion The version that was last installed.
	 */
	function upgrade($oldversion) {
	
		//Upgrade database schemas if needed
		$this->db_setup();
	}
	
	/**
	 * Upgrades SEO Ultimate to version 0.8.
	 * Version 0.8 uses 1 wp_options entry instead of 4.
	 * 
	 * @since 0.8
	 * @uses $dbdata
	 */
	function upgrade_to_08() {
		$options = array('cron', 'modules', 'settings', 'version');
		foreach ($options as $option) {
			if (($value = get_option("su_$option", false)) !== false) {
				$this->dbdata[$option] = maybe_unserialize($value);
				delete_option("su_$option");
			}
		}
	}
	
	/**
	 * WordPress will call this when the plugin is activated, as instructed by the register_activation_hook() call in {@link __construct()}.
	 * Does activation tasks for the plugin itself, not modules.
	 * 
	 * @since 0.1
	 */
	function activate() {
	
		//Nothing here yet
	}
	
	/**
	 * WordPress will call this when the plugin is deactivated, as instructed by the register_deactivation_hook() call in {@link __construct()}.
	 * 
	 * @since 0.1
	 */
	function deactivate() {
	
		//Let modules run deactivation tasks
		do_action('su_deactivate');
		
		//Unschedule all cron jobs		
		$this->remove_cron_jobs(true);
		
		//Delete module records, so that modules are re-activated if the plugin is.
		unset($this->dbdata['modules']);
		
		//Delete all cron job records, since the jobs no longer exist
		unset($this->dbdata['cron']);
		
		//Save to database
		$this->save_dbdata();
	}
	
	/**
	 * Calls module deactivation/uninstallation functions and deletes all database data.
	 * 
	 * @since 0.1
	 */
	function uninstall() {
		
		//Deactivate modules and cron jobs
		$this->deactivate();
		
		//Let modules run uninstallation tasks
		do_action('su_uninstall');
		
		//Delete all database data
		$this->dbdata = array();
		delete_option('seo_ultimate');
		
		//Stop the database data from being re-saved
		remove_action('shutdown', array($this, 'save_dbdata'));
		
		//Delete the hits table
		mysql_query("DROP TABLE IF EXISTS ".$this->get_table_name('hits'));
	}
	
	
	/********** INITIALIZATION FUNCTIONS **********/
	
	/**
	 * Fills class variables with information about where the plugin is located.
	 * 
	 * @since 0.1
	 * @uses $plugin_file_path
	 * @uses $plugin_file_url
	 * @uses $plugin_dir_path
	 * @uses $plugin_dir_url
	 */
	function load_plugin_data() {
		
		//Load plugin path/URL information
		$filename = 'seo-ultimate.php';
		$this->plugin_dir_path  = trailingslashit(dirname(trailingslashit(WP_PLUGIN_DIR).plugin_basename(__FILE__)));
		$this->plugin_file_path = $this->plugin_dir_path.$filename;
		$this->plugin_dir_url   = trailingslashit(plugins_url(dirname(plugin_basename(__FILE__))));
		$this->plugin_file_url  = $this->plugin_dir_url.$filename;
	}
	
	/**
	 * Finds and loads all modules. Runs the activation functions of newly-uploaded modules.
	 * Updates the modules list and saves it in the database. Removes the cron jobs of deleted modules.
	 * 
	 * SEO Ultimate uses a modular system that allows functionality to be added and removed on-the-fly.
	 * 
	 * @since 0.1
	 * @uses $plugin_dir_path
	 * @uses $dbdata
	 * @uses $modules Stores module classes in this array.
	 * @uses $disabled_modules
	 * @uses module_sort_callback() Passes this function to uasort() to sort the $modules array.
	 * @uses SU_MODULE_ENABLED
	 * @uses SU_MODULE_DISABLED
	 * @uses remove_cron_jobs()
	 */
	function load_modules() {
	
		//The plugin_dir_path variable must be set before calling this function!
		if (!$this->plugin_dir_path) return false;
	
		//The modules are in the "modules" subdirectory of the plugin folder.
		$dir = opendir($this->plugin_dir_path.'modules');
		
		//If no modules list is found, then create a new, empty list.
		if (!isset($this->dbdata['modules']))
			$this->dbdata['modules'] = array();
		
		//Get the modules list from last time the plugin was loaded.
		$oldmodules = $this->dbdata['modules'];
		
		//This loop will be repeated as long as there are more files to inspect
		while ($file = readdir($dir)) {
			
			//Modules are non-directory files with the .php extension
			//We need to exclude index.php or else we'll get 403s galore
			if ($file != '.' && $file != '..' && $file != 'index.php' && !is_dir($file) &&
					substr($file, -4) == '.php') {
				
				//Figure out the module's array key and class name
				$module = strval(strtolower(substr($file, 0, -4)));
				$class = 'SU_'.str_replace(' ', '', ucwords(str_replace('-', ' ', $module)));
				
				//If this module is disabled...
				if ($oldmodules[$module] == SU_MODULE_DISABLED) {
					
					$name = file($this->plugin_dir_path."modules/$file");
					if ($name) $name = str_replace(' Module', '', ltrim($name[2], ' *'));
							else $name = ucwords(str_replace('-', ' ', $module));
					
					$this->disabled_modules[$module] = __($name, 'seo-ultimate');
					
				} else {
				
					//Load the module's code
					require_once("modules/$file");
				
					//If this is actually a module...
					if (class_exists($class)) {
					
						//Create an instance of the module's class and store it in the array
						$this->modules[$module] = new $class;
						
						//We must tell the module what its key is so that it can save settings
						$this->modules[$module]->module_key = $module;
						
						//Tell the module what its URL is
						$this->modules[$module]->module_url = $this->plugin_dir_url."modules/$file";
						
						//Tell the module what its plugin page hook is
						$this->modules[$module]->plugin_page_hook =
							$this->modules[$module]->get_menu_parent_hook().'_page_'.SEO_Ultimate::key_to_hook($module);
						
					} //If this isn't a module, then the file will simply be included as-is
				} 
			}
		}
		
		//If the loop above found modules, then sort them with our special sorting function
		//so they appear on the admin menu in the right order
		if (count($this->modules) > 0)
			uasort($this->modules, array($this, 'module_sort_callback'));
		
		//Now we'll compare the current module set with the one from last time.
		
		//Construct the new modules list that'll go in the database.
		//This code block will add/activate new modules, keep existing ones, and remove (i.e. not add) deleted ones.
		foreach ($this->modules as $key => $module) {
			if (isset($oldmodules[$key])) {
				$newmodules[$key] = $oldmodules[$key];
			} else {
				$module->activate();
				$newmodules[$key] = SU_MODULE_ENABLED;
			}
		}
		
		//Register disabled modules as such
		foreach ($this->disabled_modules as $key => $name) {
			$newmodules[$key] = SU_MODULE_DISABLED;
		}
		
		//Save the new modules list
		$this->dbdata['modules'] = $newmodules;
		
		//Remove the cron jobs of deleted modules
		$this->remove_cron_jobs();
	}
	
	/**
	 * Runs during WordPress's init action.
	 * Loads the textdomain and calls modules' initialization functions.
	 * 
	 * @since 0.1
	 * @uses $plugin_file_path
	 * @uses SU_Module::load_default_settings()
	 * @uses SU_Module::init()
	 */
	function init() {
		
		//Allow translation of this plugin
		load_plugin_textdomain('seo-ultimate', '', plugin_basename($this->plugin_file_path));
		
		//Load default module settings and run modules' init tasks
		foreach ($this->modules as $module) {
			$module->load_default_settings();
			$module->init();
		}
	}
	
	
	/********** DATABASE FUNCTIONS **********/
	
	/**
	 * Will create or update the database table.
	 * 
	 * @since 0.1
	 * @uses get_table_name()
	 */
	function db_setup() {
	
		$sql = "CREATE TABLE " . $this->get_table_name('hits') . " (
			id BIGINT NOT NULL AUTO_INCREMENT,
			time INT NOT NULL ,
			ip_address VARCHAR(255) NOT NULL,
			user_agent VARCHAR(255) NOT NULL,
			url TEXT NOT NULL,
			redirect_url TEXT NOT NULL,
			redirect_trigger VARCHAR(255) NOT NULL,
			referer TEXT NOT NULL,
			status_code SMALLINT(3) NOT NULL,
			is_new BOOL NOT NULL,
			PRIMARY KEY  (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	/**
	 * Returns a full, prefixed MySQL table name.
	 * 
	 * @since 0.1
	 * 
	 * @param string $shortname The non-prefixed table name.
	 * @return string The full, prefixed table name.
	 */
	function get_table_name($shortname) {
		global $wpdb;
		if ($shortname == 'hits')
			return $wpdb->prefix . "sds_hits";
		else
			return '';
	}
	
	/**
	 * Saves settings data to the database.
	 * 
	 * @since 0.8
	 * @uses $dbdata
	 */
	function save_dbdata() {
		
		//If we don't clear the cache, we sometimes end up with multiple seo_ultimate options entries
		wp_cache_delete('seo_ultimate', 'options');
		wp_cache_delete('notoptions', 'options');
		
		if (empty($this->dbdata))
			//If we don't have any data to save, then delete the option instead of saving an empty array
			delete_option('seo_ultimate');
		else
			//Save our data to the database
			update_option('seo_ultimate', $this->dbdata);
	}
	
	/**
	 * Saves the hit data to the database.
	 * 
	 * @since 0.9
	 * @uses $hit
	 */
	function save_hit() {
		global $wpdb;
		if (!empty($this->hit))
			$wpdb->insert($this->get_table_name('hits'), $this->hit);
	}
	
	/**
	 * Saves information about the current hit into an array, which is later saved to the database.
	 * 
	 * @since 0.1
	 * @uses get_current_url()
	 * @uses $hit_id
	 * 
	 * @param string $status_header The full HTTP status header. Unused and returned as-is.
	 * @param int $status_code The numeric HTTP status code.
	 * @param string $redirect_url The URL to which the visitor is being redirected. Optional.
	 * @return string Returns the $status_header variable unchanged.
	 */
	function log_hit($status_header, $status_code, $redirect_url = '') {
		
		//Only log hits from non-logged-in users
		if (!is_user_logged_in()) {
			global $wpdb;
			
			//Get the current URL
			$url = $this->get_current_url();
			
			//Have we seen this URL before?
			$table = $this->get_table_name('hits');
			$is_new = (count($wpdb->get_results($wpdb->prepare("SELECT url FROM $table WHERE url = %s AND is_new = 0", $url))) == 0);
			
			//Put it all into an array
			$data = array(
				  'time' => time()
				, 'ip_address' => $_SERVER['REMOTE_ADDR']
				, 'user_agent' => $_SERVER['HTTP_USER_AGENT']
				, 'url' => $url
				, 'redirect_url' => $redirect_url
				, 'redirect_trigger' => $this->hit_redirect_trigger
				, 'referer' => $_SERVER['HTTP_REFERER']
				, 'status_code' => $status_code
				, 'is_new' => $is_new
			);
			
			//We don't want to overwrite a redirect URL if it's already been logged
			if (strlen($this->hit['redirect_url']))
				$data['redirect_url'] = $this->hit['redirect_url'];
			
			//Put the hit data into our variable.
			//We'll save it to the database later, since the hit data may change as we gather further information
			//(e.g. when the redirect URL is discovered).
			$this->hit = $data;
		}
		
		//This function can be used as a WordPress filter, so we return the needed variable.
		return $status_header;
	}
	
	/**
	 * A wp_redirect WordPress filter that logs the URL to which the visitor is being redirected.
	 * 
	 * @since 0.2
	 * @uses log_hit()
	 * 
	 * @param string $redirect_url The URL to which the visitor is being redirected.
	 * @param int $status_code The numeric HTTP status code.
	 * @return string The unchanged $redirect_url parameter.
	 */
	function log_redirect($redirect_url, $status_code) {
		if (empty($this->hit_redirect_trigger)) $this->hit_redirect_trigger = 'wp_redirect';
		$this->log_hit(null, $status_code, $redirect_url); //We call log_hit() again so we can pass along the redirect URL
		return $redirect_url;
	}
	
	/**
	 * A redirect_canonical WordPress filter that logs the fact that a canonical redirect is being issued.
	 * 
	 * @since 0.3
	 * @uses log_hit()
	 * 
	 * @param string $redirect_url The URL to which the visitor is being redirected.
	 * @return string The unchanged $redirect_url parameter.
	 */
	function log_redirect_canonical($redirect_url) {
		if (empty($this->hit_redirect_trigger)) $this->hit_redirect_trigger = 'redirect_canonical';
		return $redirect_url;
	}
	
	
	/********** ADMIN MENU FUNCTIONS **********/
	
	/**
	 * Constructs the "SEO" menu and its subitems.
	 * 
	 * @since 0.1
	 * @uses $modules
	 * @uses get_module_count_code()
	 * @uses SU_Module::get_menu_count()
	 * @uses SU_Module::get_menu_pos()
	 * @uses SU_Module::get_menu_title()
	 * @uses SU_Module::get_page_title()
	 * @uses key_to_hook()
	 */
	function add_menus() {
	
		//If subitems have numeric bubbles, then add them up and show the total by the main menu item
		$count = 0;
		foreach ($this->modules as $key => $module) {
			if ($this->dbdata['modules'][$key] > SU_MODULE_SILENCED && $module->get_menu_count() > 0 && $module->get_menu_parent() == 'seo')
				$count += $module->get_menu_count();
		}
		$count_code = $this->get_menu_count_code($count);		
		
		//Add the "SEO" menu item!
		add_utility_page(__('SEO Ultimate', 'seo-ultimate'), __('SEO', 'seo-ultimate').$count_code, 'manage_options', 'seo', array(), 'div');
		
		//Translations and count codes will mess up the admin page hook, so we need to fix it manually.
		global $admin_page_hooks;
		$admin_page_hooks['seo'] = 'seo';
		
		//Add all the subitems
		foreach ($this->modules as $file => $module) {
		
			//Show a module on the menu only if it provides a menu title and it doesn't have a parent module
			if ($module->get_menu_title() && !$module->get_parent_module()) {
				
				//If the module is hidden, put the module under a non-existant menu parent
				//(this will let the module's admin page be loaded, but it won't show up on the menu)
				if ($this->dbdata['modules'][$file] > SU_MODULE_HIDDEN)
					$parent = $module->get_menu_parent();
				else
					$parent = 'su-hidden-modules';
				
				if ($this->dbdata['modules'][$file] > SU_MODULE_SILENCED)
					$count_code = $this->get_menu_count_code($module->get_menu_count());
				else
					$count_code = '';
				
				add_submenu_page($parent, $module->get_page_title(), $module->get_menu_title().$count_code,
					'manage_options', $this->key_to_hook($file), array($module, 'admin_page'));
			}
		}
	}
	
	/**
	 * Compares two modules to determine which of the two should be displayed first on the menu.
	 * Sorts by menu position first, and title second.
	 * Works as a uasort() callback.
	 * 
	 * @since 0.1
	 * @uses SU_Module::get_menu_pos()
	 * @uses SU_Module::get_menu_title()
	 * 
	 * @param SU_Module $a The first module to compare.
	 * @param SU_Module $b The second module to compare.
	 * @return int This will be -1 if $a comes first, or 1 if $b comes first.
	 */
	function module_sort_callback($a, $b) {
		if ($a->get_menu_pos() == $b->get_menu_pos()) {
			return strcmp($a->get_menu_title(), $b->get_menu_title());
		}
		
		return ($a->get_menu_pos() < $b->get_menu_pos()) ? -1 : 1;
	}
	
	/**
	 * If the bubble alert count parameter is greater than zero, then returns the HTML code for a numeric bubble to display next to a menu item.
	 * Otherwise, returns an empty string.
	 * 
	 * @since 0.1
	 * 
	 * @param int $count The number that should appear in the bubble.
	 * @return string The string that should be added to the end of the menu item title.
	 */
	function get_menu_count_code($count) {
	
		//If we have alerts that need a bubble, then return the bubble HTML.
		if ($count > 0)
			return "&nbsp;<span id='awaiting-mod' class='count-$count'><span class='pending-count'>$count</span></span>";
		else
			return '';
	}
	
	/**
	 * Converts a module key to a menu hook.
	 * (Makes the "Module Manager" module load when the "SEO" parent item is clicked.)
	 * 
	 * @since 0.1
	 * 
	 * @param string $key The module key.
	 * @return string The menu hook.
	 */
	function key_to_hook($key) {
		switch ($key) {
			case 'modules': return 'seo'; break;
			case 'settings': return 'seo-ultimate'; break;
			default: return "su-$key"; break;
		}
	}
	
	/**
	 * Converts a menu hook to a module key.
	 * (If the "SEO" parent item is clicked, then the Module Manager is being shown.)
	 * 
	 * @since 0.1
	 * 
	 * @param string $hook The menu hook.
	 * @return string The module key.
	 */
	function hook_to_key($hook) {
		switch ($hook) {
			case 'seo': return 'modules'; break;
			case 'seo-ultimate': return 'settings'; break;
			default: return substr($hook, 3); break;
		}
	}
	
	
	/********** OTHER ADMIN FUNCTIONS **********/
	
	/**
	 * Returns a boolean indicating whether the user is currently viewing an admin page generated by this plugin.
	 * 
	 * @since 0.1
	 * 
	 * @return bool Whether the user is currently viewing an admin page generated by this plugin.
	 */
	function is_plugin_admin_page() {
		if (is_admin()) {
			global $plugin_page;
			
			foreach ($this->modules as $key => $module) {
				if (strcmp($plugin_page, $this->key_to_hook($key)) == 0) return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Includes the plugin's CSS and JavaScript in the header.
	 * Also includes a module's CSS/JavaScript on its administration page.
	 * 
	 * @todo Link to global plugin includes only when on plugin pages.
	 * 
	 * @since 0.1
	 * @uses $modules
	 * @uses $plugin_file_url
	 * @uses $plugin_dir_url
	 * @uses hook_to_key()
	 */
	function admin_includes() {
		
		//Global CSS
		echo "\n<link rel='stylesheet' type='text/css' href='".$this->plugin_dir_url."global.css?v=".SU_VERSION."' />\n";
		
		//Figure out what plugin admin page we're on
		global $plugin_page;
		$pp = $this->hook_to_key($plugin_page);
		
		foreach ($this->modules as $key => $module) {
			
			//Does the current admin page belong to this module?
			if (strcmp($key, $pp) == 0) {
				
				//We're viewing a module page, so print links to the CSS/JavaScript files loaded for all modules
				echo "\n<link rel='stylesheet' type='text/css' href='".$this->plugin_dir_url."modules.css?v=".SU_VERSION."' />\n";
				echo "\n<script type='text/javascript' src='".$this->plugin_dir_url."modules.js?v=".SU_VERSION."'></script>\n";
				
				//Print links to the module's CSS and JavaScript.
				echo "\n<link rel='stylesheet' type='text/css' href='".$module->module_url."?css=admin&amp;v=".SU_VERSION."' />\n";
				echo "\n<script type='text/javascript' src='".$module->module_url."?js=admin&amp;v=".SU_VERSION."'></script>\n";
				
				//The module has been found; mission accomplished.
				return;
			}
		}
	}
	
	/**
	 * Replaces WordPress's default contextual help with postmeta help when appropriate.
	 * 
	 * @since 0.1
	 * @uses $modules
	 * 
	 * @param string $text WordPress's default contextual help.
	 * @param string $screen The screen currently being shown.
	 * @return string The contextual help content that should be shown.
	 */
	function admin_help($text, $screen) {
		
		//If we're on the post or page editor...
		if (strcmp($screen, 'post') == 0 || strcmp($screen, 'page') == 0) {
		
			//Gather post meta help content
			$helparray = apply_filters('su_postmeta_help', array());
			
			if ($helparray) {
			
				$customhelp = '';
				foreach ($helparray as $line) {
					$customhelp .= "<li><p>$line</p></li>\n";
				}
				
				$text .= "<div class='su-help'>\n";
				$text .= '<h5>'.__('SEO Settings Help', 'seo-ultimate')."</h5>\n";
				$text .= "<div class='metabox-prefs'>\n";
				$text .= "<p>".__("The SEO Settings box lets you customize these settings:", 'seo-ultimate')."</p>\n";
				$text .= "<ul>\n$customhelp\n</ul>";
				$text .= "<p><em>".__("(The SEO Settings box is part of the SEO Ultimate plugin.)", 'seo-ultimate')."</em></p>\n";
				$text .= "\n</div>\n</div>\n";
				return $text;
			}
		}
		
		//No custom help content to show. Return the default.
		return $text;
	}
	
	/**
	 * Notifies the user if he/she is using plugins whose functionality SEO Ultimate replaces.
	 * 
	 * @since 0.1
	 * @uses plugin_page_notice() Hooked into the after_plugin_row_$path actions.
	 */
	function plugin_page_notices() {
		
		if (isset($this->modules['settings']) && !$this->modules['settings']->get_setting('plugin_notices'))
			return;
		
		global $pagenow;
		
		if ($pagenow == 'plugins.php') {
		
			$r_plugins = array(
				  'all-in-one-seo-pack/all_in_one_seo_pack.php' //Title Rewriter, Meta Editor, Noindex Manager
				, 'another-wordpress-meta-plugin/another_wordpress_meta_plugin.php' //Meta Editor
				, 'canonical/canonical.php' //Canonicalizer
				, 'noindex-login/noindex-login.php' //Noindex Manager
				, 'search-engine-verify/search-engine-verify.php' //Meta Editor
			);
			
			$i_plugins = get_plugins();
			
			foreach ($r_plugins as $path) {
				if (isset($i_plugins[$path]))
					add_action("after_plugin_row_$path", array($this, 'plugin_page_notice'), 10, 3);
			}
		}
	}
	
	/**
	 * Outputs a table row notifying the user that he/she is using a plugin whose functionality SEO Ultimate replaces.
	 * 
	 * @since 0.1
	 */
	function plugin_page_notice($file, $data, $context) {
		if (is_plugin_active($file)) {
			
			//3 columns if 2.8+ but 5 columns if 2.7.x or prior
			global $wp_version;
			$columns = version_compare($wp_version, '2.8', '>=') ? 3 : 5;
			
			echo "<tr class='plugin-update-tr su-plugin-notice'><td colspan='$columns' class='plugin-update'><div class='update-message'>\n";
			printf(__('SEO Ultimate includes the functionality of %1$s. You may want to deactivate %1$s to avoid plugin conflicts.', 'seo-ultimate'), $data['Name']);
			echo "</div></td></tr>\n";
		}
	}
	
	/**
	 * Displays new-version info in this plugin's update row on WordPress's plugin admin page.
	 * Hooked into WordPress's in_plugin_update_message-(file) action.
	 * 
	 * @since 0.1
	 * 
	 * @param array $plugin_data An array of this plugin's information. Unused.
	 * @param obejct $r The response object from the WordPress Plugin Directory.
	 */
	function plugin_update_info($plugin_data, $r) {
		if ($r && $r->new_version) {
			$info = $this->load_webpage("http://www.seodesignsolutions.com/apis/su/update-info/?ov=".urlencode(SU_VERSION)."&nv=".urlencode($r->new_version));
			if ($info) {
				$info = strip_tags($info, "<br><a><b><i><span>");
				echo "<br />$info";
			}
		}
	}
	
	
	/********** ADMIN POST META BOX FUNCTIONS **********/
	
	/**
	 * Gets the post meta box fields from the modules, sorts them, and returns the HTML as a string.
	 * 
	 * @since 0.1
	 * @uses $modules
	 * 
	 * @param string $screen The admin screen currently being viewed (post, page). Defaults to post. Optional.
	 * @return string Concatenated <tr>(field)</tr> strings.
	 */
	function get_postmeta_fields($screen='post') {
		
		$fields = $this->get_postmeta_array($screen);
		
		if (count($fields) > 0) {
		
			//Sort the fields array
			ksort($fields, SORT_STRING);
			
			//Return a string
			return implode("\n", $fields);
		}
		
		return '';
	}
	
	/**
	 * Compiles the post meta box field array based on data provided by the modules.
	 * 
	 * @since 0.8
	 * @uses SU_Module::postmeta_fields()
	 * 
	 * @param string $screen The admin screen currently being viewed (post, page). Defaults to post. Optional.
	 * @return array An array of post meta HTML.
	 */
	function get_postmeta_array($screen='post') {
		$fields = array();
		foreach ($this->modules as $module)
			$fields = $module->postmeta_fields($fields, $screen);
		return $fields;
	}
	
	/**
	 * If we have post meta fields to display, then register our meta box with WordPress.
	 * 
	 * @since 0.1
	 * @uses get_postmeta_fields()
	 */
	function add_postmeta_box() {
		
		//Add the metabox to posts and pages.
		foreach (array('post', 'page') as $screen) {
		
			//Only show the meta box if there are fields to show.
			if ($this->get_postmeta_fields($screen))
				add_meta_box('su_postmeta', __('SEO Settings', 'seo-ultimate'), array($this, "show_{$screen}_postmeta_box"), $screen, 'normal', 'high');
		}
	}
	
	/**
	 * Displays the inner contents of the post meta box when editing posts.
	 * 
	 * @since 0.1
	 * @uses show_postmeta_box()
	 */
	function show_post_postmeta_box() {
		$this->show_postmeta_box('post');
	}
	
	/**
	 * Displays the inner contents of the post meta box when editing Pages.
	 * 
	 * @since 0.1
	 * @uses show_postmeta_box()
	 */
	function show_page_postmeta_box() {
		$this->show_postmeta_box('page');
	}
	
	/**
	 * Displays the inner contents of the post meta box.
	 * 
	 * @since 0.1
	 * @uses get_postmeta_fields()
	 * 
	 * @param string $screen The admin screen currently being viewed (post, page).
	 */
	function show_postmeta_box($screen) {
	
		//Begin box
		echo "<div id='su-postmeta-box'>\n";
		wp_nonce_field('su-update-postmeta', '_su_wpnonce');
		echo "\n<table>\n";
		
		//Output postmeta fields
		echo $this->get_postmeta_fields($screen);
		
		//End box
		echo "\n</table>\n</div>\n";
	}
	
	/**
	 * Saves the values of the fields in the post meta box.
	 * 
	 * @since 0.1
	 * 
	 * @param int $post_id The ID of the post being saved.
	 * @param object $post The post being saved.
	 */
	function save_postmeta_box($post_id, $post) {
		
		//Sanitize
		$post_id = (int)$post_id;
		
		//Run preliminary permissions checks
		if ( !wp_verify_nonce($_REQUEST['_su_wpnonce'], 'su-update-postmeta') ) return;
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id )) return;
		} elseif ( 'post' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_post', $post_id )) return;
		} else return;
		
		//Get an array of the postmeta fields
		$keys = array_keys($this->get_postmeta_array($_POST['post_type']));
		$fields = array();
		
		foreach ($keys as $key) {
			$newfields = explode('|', $key);
			array_shift($newfields);
			$fields = array_merge($fields, $newfields);
		}
		
		//Update postmeta values
		foreach ($fields as $field) {
			
			$metakey = "_su_$field";
			
			//Delete the old value
			delete_post_meta($post_id, $metakey);
			
			//Add the new value, if there is one
			$value = $_POST[$metakey];
			if (!empty($value)) {
				add_post_meta($post_id, $metakey, $value);
			}
		}
	}
	
	
	/********** CRON FUNCTION **********/
	
	/**
	 * Can remove cron jobs for modules that no longer exist, or remove all cron jobs.
	 * 
	 * @since 0.1
	 * 
	 * @param bool $remove_all Whether to remove all cron jobs. Optional.
	 */
	function remove_cron_jobs($remove_all = false) {
		$crondata = $this->dbdata['cron'];
		if (is_array($crondata)) {
			$newcrondata = $crondata;
			
			foreach ($crondata as $key => $crons) {
				if ($remove_all || !isset($this->modules[$key])) {
					foreach ($crons as $data) { wp_clear_scheduled_hook($data[0]); }
					unset($newcrondata[$key]);
				}
			}
			
			$this->dbdata['cron'] = $newcrondata;
		}
	}
	
	
	/********** TEMPLATE OUTPUT FUNCTION **********/
	
	/**
	 * Outputs code into the template's <head> tag.
	 * 
	 * @since 0.1
	 */
	function template_head() {
		
		if (isset($this->modules['settings']))
			$markcode = $this->modules['settings']->get_setting('mark_code');
		else
			$markcode = false;
		
		echo "\n";
		
		if ($markcode) echo "\n<!-- ".SU_PLUGIN_NAME." (".SU_PLUGIN_URI.") -->\n";
		
		//Let modules output head code.
		do_action('su_head');
		
		//Make sure the blog is public. Telling robots what to do is a moot point if they aren't even seeing the blog.
		if (get_option('blog_public')) {
			$robots = implode(',', apply_filters('su_meta_robots', array()));
			if ($robots) echo "\t<meta name=\"robots\" content=\"$robots\" />\n";
		}
		
		if ($markcode) echo "<!-- /".SU_PLUGIN_NAME." -->\n\n";
	}
	
	/********** PSEUDO-STATIC FUNCTIONS **********/
	
	/**
	 * Approximately determines the URL in the visitor's address bar. (Includes query strings, but not #anchors.)
	 * 
	 * @since 0.1
	 * 
	 * @return string The current URL.
	 */
	function get_current_url() {
		$url = 'http';
		if ($_SERVER["HTTPS"] == "on") $url .= "s";
		$url .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80")
			return $url.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
			return $url.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	/**
	 * Determines the ID of the current post.
	 * Works in the admin as well as the front-end.
	 * 
	 * @since 0.2
	 * 
	 * @return int|false The ID of the current post, or false on failure.
	 */
	function get_post_id() {
		if (is_admin())
			return intval($_REQUEST['post']);
		elseif (in_the_loop())
			return intval(get_the_ID());
		elseif (is_singular()) {
			global $wp_query;
			return $wp_query->get_queried_object_id();
		}
		
		return false;
	}
	
	/**
	 * Loads a webpage and returns its HTML as a string.
	 * 
	 * @since 0.3
	 * 
	 * @param string $url The URL of the webpage to load.
	 * @return string The HTML of the URL.
	 */
	function load_webpage($url) {
		
		$options = array();
		$options['headers'] = array(
			'User-Agent' => su_get_user_agent()
		);
		
		$response = wp_remote_request($url, $options);
		
		if ( is_wp_error( $response ) ) return false;
		if ( 200 != $response['response']['code'] ) return false;
		
		return trim( $response['body'] );
	}
	
	/**
	 * Uses the Google Chart API to output a 3D pie chart.
	 * 
	 * @since 0.3
	 * 
	 * @param array $data The labels (keys) and values that go on the pie chart.
	 * @param array|string $color An array or string of which color(s) to use on the pie chart.
	 */
	function pie_chart_3d($data, $color = '0000FF') {
		$labels = implode('|', array_keys($data));
		$values = implode(',', array_values($data));
		$colors = implode(',', (array)$color);
		echo "<img src='http://chart.apis.google.com/chart?cht=p3&amp;chd=t:$values&amp;chs=250x100&amp;chl=$labels&amp;chco=$colors' alt='' />";
	}
}
?>