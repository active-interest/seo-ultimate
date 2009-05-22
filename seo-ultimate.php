<?php
/*
Plugin Name: SEO Ultimate
Plugin URI: http://www.seodesignsolutions.com/wordpress-seo/
Description: This all-in-one SEO plugin can rewrite title tags and add noindex to pages (with many more features coming soon).
Version: 0.1
Author: SEO Design Solutions
Author URI: http://www.seodesignsolutions.com/
Text Domain: seo-ultimate
*/

/**
 * The main SEO Ultimate plugin file.
 * @package SeoUltimate
 * @version 0.1
 * @link http://www.seodesignsolutions.com/wordpress-seo/ SEO Ultimate Homepage
 */

/*
Copyright © 2009 John Lamansky

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/********** CONSTANTS **********/

define("SU_PLUGIN_NAME", "SEO Ultimate");
define("SU_PLUGIN_URI", "http://www.seodesignsolutions.com/wordpress-seo/");
define("SU_VERSION", "0.1");
define("SU_AUTHOR", "SEO Design Solutions");
define("SU_AUTHOR_URI", "http://www.seodesignsolutions.com/");
define("SU_USER_AGENT", "SeoUltimate/0.1");


/********** CLASSES **********/

/**
 * The main class. Provides plugin-level functionality.
 */
class SEO_Ultimate {

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
	 * Stores the status (disabled/hidden/silenced/enabled) of each module.
	 * 
	 * @since 0.1
	 * @var array
	 */
	var $module_status = array();
	
	/**
	 * The server path of this plugin file.
	 * Example: /home/user/public_html/wp-content/plugins/seo-ultimate/seo-ultimate.php
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $plugin_file_path;
	
	/**
	 * The public URL of this plugin file.
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
	
	
	/********** CLASS CONSTRUCTORS **********/
	
	/**
	 * Fills in class variables, loads modules, and hooks into WordPress.
	 * PHP5-style constructor.
	 * 
	 * @since 0.1
	 * @uses SU_VERSION
	 * @uses install()
	 * @uses upgrade()
	 * @uses load_plugin_data()
	 * @uses load_modules()
	 * @uses activate() Registered with WordPress as the activation hook.
	 * @uses init() Hooked into WordPress's "init" action.
	 * @uses add_menus() Hooked into WordPress's "admin_menu" action.
	 * @uses sanitize_menu_hook() Hooked into WordPress's "sanitize_title" filter.
	 * @uses admin_includes() Hooked into WordPress's "admin_head" action.
	 * @uses plugin_page_notices() Hooked into WordPress's "admin_head" action.
	 * @uses admin_help() Hooked into WordPress's "contextual_help" action.
	 * @uses log_hit() Hooked into WordPress's "status_header" action.
	 */
	function __construct() {
		
		/********** VERSION CHECKING **********/
		
		//Get the current version, and the version when the plugin last ran
		$version = SU_VERSION;
		$oldversion = get_option('su_version', false);
		
		//If this is the first time the plugin is running, then install()
		if ($oldversion === false)
			$this->install();
		
		//If $oldversion is less than $version, then upgrade()
		elseif (version_compare($version, $oldversion) == 1)
			$this->upgrade($oldversion);
		
		//Store the current version in the database.
		//Rest assured, WordPress won't waste a database query if the value hasn't changed.
		update_option('su_version', $version);
		
		
		/********** INITIALIZATION **********/
		
		//Load data about the plugin file itself
		$this->load_plugin_data();
		
		//Load plugin modules. Must be called *after* load_plugin_data()
		$this->load_modules();
		
		
		/********** PLUGIN EVENT HOOKS **********/
		
		//If we're activating the plugin, then call the activation function
		register_activation_hook(__FILE__, array($this, 'activate'));
		
		//If we're deactivating the plugin, then call the deactivation function
		register_deactivation_hook(__FILE__, array($this, 'deactivate'));
		
		//If we're uninstalling the plugin, then call the uninstallation function
		register_uninstall_hook(__FILE__, 'su_uninstall');
		
		
		/********** ACTION & FILTER HOOKS **********/
		
		//Initializes modules at the init action
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
		add_action('contextual_help', array($this, 'admin_help'), 10, 2);
		
		//Postmeta box hooks
		add_action('admin_menu', array($this, 'add_postmeta_box'));
		add_action('save_post',  array($this, 'save_postmeta_box'), 10, 2);
		
		//Display info on new versions
		add_action('in_plugin_update_message-'.plugin_basename(__FILE__), array($this, 'plugin_update_info'), 10, 2);
		
		//Log this visitor!
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
	}
	
	/**
	 * This will be called if the plugin's version has increased since the last run.
	 * 
	 * @since 0.1
	 */
	function upgrade() {
	
		//Upgrade database schemas if needed
		$this->db_setup();
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
		delete_option('su_modules');
		
		//Delete all cron job records, since the jobs no longer exist
		delete_option('su_cron');
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
		
		//Delete all other options that aren't deleted in deactivate()
		delete_option('su_version');
		delete_option('su_settings');
		
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
		$this->plugin_file_path = trailingslashit(WP_PLUGIN_DIR).plugin_basename(__FILE__);
		$this->plugin_file_url  = plugins_url(plugin_basename(__FILE__)); //trailingslashit(WP_PLUGIN_URL).
		$this->plugin_dir_path  = trailingslashit(dirname($this->plugin_file_path));
		$this->plugin_dir_url   = trailingslashit(plugins_url(dirname(plugin_basename(__FILE__))));
	}
	
	/**
	 * Finds and loads all modules. Runs the activation functions of newly-uploaded modules.
	 * Updates the modules list and saves it in the database. Removes the cron jobs of deleted modules.
	 * 
	 * @since 0.1
	 * @uses $plugin_dir_path
	 * @uses $modules Stores module classes in this array.
	 * @uses module_sort_callback() Passes this function to uasort() to sort the $modules array.
	 * @uses SU_MODULE_ENABLED
	 * @uses SU_MODULE_DISABLED
	 */
	function load_modules() {
	
		//The plugin_dir_path variable must be set before calling this function!
		if (!$this->plugin_dir_path) return false;
	
		//The modules are in the "modules" subdirectory of the plugin folder.
		$dir = opendir($this->plugin_dir_path.'modules');
		
		//Get the modules list from last time the plugin was loaded.
		$oldmodules = maybe_unserialize(get_option('su_modules', false));
		
		//If no list is found, then create a new, empty list.
		if ($oldmodules === false) {
			$oldmodules = array();
			add_option('su_modules', serialize($oldmodules));
		}
		
		//This loop will be repeated as long as there are more files to inspect
		while ($file = readdir($dir)) {
			
			//Modules are non-directory files with the .php extension
			if ($file != '.' && $file != '..' && !is_dir($file) &&
					substr($file, -4) == '.php') {
				
				//Figure out the module's array key and class name
				$module = strval(strtolower(substr($file, 0, -4)));
				$class = 'SU_'.str_replace(' ', '', ucwords(str_replace('-', ' ', $module)));
				
				//If this module is disabled...
				if ($oldmodules[$module] == SU_MODULE_DISABLED) {
					
					$name = file($this->plugin_dir_path."modules/$file");
					if ($name) $name = str_replace(' Module', '', ltrim($name[2], ' *'));
							else $name = ucwords(str_replace('-', ' ', $module));
					
					$this->disabled_modules[$module] = $name;
					
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
		
		foreach ($this->disabled_modules as $key => $name) {
			$newmodules[$key] = SU_MODULE_DISABLED;
		}
		
		//Save the new modules list
		$this->module_status = $newmodules;
		update_option('su_modules', serialize($newmodules));
		
		//Remove the cron jobs of deleted modules
		$this->remove_cron_jobs();
	}
	
	/**
	 * Runs during WordPress's init action.
	 * Loads the textdomain and calls modules' init_pre() functions.
	 * 
	 * @since 0.1
	 * @uses SU_Module::init_pre()
	 */
	function init() {
		
		//Allow translation of this plugin
		load_plugin_textdomain('seo-ultimate', '', plugin_basename(__FILE__));
		
		//Let the modules run init tasks
		foreach ($this->modules as $module)
			$module->init_pre();
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
	 * A status_header WordPress filter that logs the current hit.
	 * 
	 * @since 0.1
	 * @uses get_current_url()
	 * 
	 * @param string $status_header The full HTTP status header. Unused and returned as-is.
	 * @param int $status_code The numeric HTTP status code.
	 * @return string Returns the $status_header variable unchanged.
	 */
	function log_hit($status_header, $status_code) {
		if (!is_user_logged_in()) {
			global $wpdb;
			
			$table = $this->get_table_name('hits');
			$url = $this->get_current_url();
			$is_new = (count($wpdb->get_results($wpdb->prepare("SELECT url FROM $table WHERE url = %s AND is_new = 0", $url))) == 0);
			
			$wpdb->query(
				$wpdb->prepare( "INSERT INTO $table ( time, ip_address, user_agent, url, status_code, is_new ) VALUES ( %d, %s, %s, %s, %d, %d )",
					time(), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $url, $status_code, $is_new )
			);
		}
		
		return $status_header;
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
			if ($this->module_status[$key] > SU_MODULE_SILENCED && $module->get_menu_count() > 0 && $module->get_menu_parent() == 'seo')
				$count += $module->get_menu_count();
		}
		$count_code = $this->get_menu_count_code($count);		
		
		//Add the "SEO" menu item!
		if (isset($this->modules['stats'])) $primarykey = 'stats'; else $primarykey = 0;
		add_utility_page(__('SEO Ultimate', 'seo-ultimate'), __('SEO', 'seo-ultimate').$count_code, 'manage_options', 'seo', array($this->modules[$primarykey], 'admin_page'), 'div');
		
		//Translations and count codes will mess up the admin page hook, so we need to fix it manually.
		global $admin_page_hooks;
		$admin_page_hooks['seo'] = 'seo';
		
		//Add all the subitems
		foreach ($this->modules as $file => $module) {
		
			//Show a module on the menu only if it provides a menu title
			if ($module->get_menu_title()) {
				
				//If the module is hidden, put the module under a non-existant menu parent
				//(this will let the module's admin page be loaded, but it won't show up on the menu)
				if ($this->module_status[$file] > SU_MODULE_HIDDEN)
					$parent = $module->get_menu_parent();
				else
					$parent = 'su-hidden-modules';
				
				if ($this->module_status[$file] > SU_MODULE_SILENCED)
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
	 * (Makes the "Stats" module load when the "SEO" parent item is clicked.)
	 * 
	 * @since 0.1
	 * 
	 * @param string $key The module key.
	 * @return string The menu hook.
	 */
	function key_to_hook($key) {
		switch ($key) {
			case 'stats': return 'seo'; break;
			case 'settings': return 'seo-ultimate'; break;
			default: return "su-$key"; break;
		}
	}
	
	/**
	 * Converts a menu hook to a module key.
	 * (If the "SEO" parent item is clicked, then the Stats module is being shown.)
	 * 
	 * @since 0.1
	 * 
	 * @param string $hook The menu hook.
	 * @return string The module key.
	 */
	function hook_to_key($hook) {
		switch ($hook) {
			case 'seo': return 'stats'; break;
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
				if ($plugin_page == $this->key_to_hook($key)) return true;
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
	
		//Global plugin CSS and JavaScript
		echo "\n<link rel='stylesheet' type='text/css' href='".$this->plugin_dir_url."seo-ultimate.css?version=".SU_VERSION."' />\n";
		echo "\n<script type='text/javascript' src='".$this->plugin_dir_url."seo-ultimate.js?version=".SU_VERSION."'></script>\n";
		
		//Figure out what plugin admin page we're on
		global $plugin_page;
		$pp = $this->hook_to_key($plugin_page);
		
		foreach ($this->modules as $key => $module) {
			
			//Is the current admin page belong to this module? If so, print links to the module's CSS and JavaScript.
			if (strcmp($key, $pp) == 0) {
				echo "\n<link rel='stylesheet' type='text/css' href='".$module->module_url."?css=admin&amp;version=".SU_VERSION."' />\n";
				echo "\n<script type='text/javascript' src='".$module->module_url."?js=admin&amp;version=".SU_VERSION."'></script>\n";
				return;
			}
		}
	}
	
	/**
	 * Replaces WordPress's default contextual help with module-specific help text, if the module provides it.
	 * 
	 * @since 0.1
	 * @uses $modules
	 * 
	 * @param string $text WordPress's default contextual help.
	 * @param string $screen The screen currently being shown.
	 * @return string The contextual help content that should be shown.
	 */
	function admin_help($text, $screen) {
		//If $screen begins with a recognized prefix...
		if ($screen == 'toplevel_page_seo' || substr($screen, 0, 9) == 'seo_page_' || substr($screen, 0, 14) == 'settings_page_') {
		
			//Remove the prefix from $screen to get the $key
			$key = $this->hook_to_key(str_replace(array('toplevel_page_', 'seo_page_', 'settings_page_'), '', $screen));
			
			//If $key refers to a module...
			if (isset($this->modules[$key])) {
			
				//Ask the module for custom help content
				$customhelp = $this->modules[$key]->admin_help();
				
				//If we have custom help to display...
				if ($customhelp !== false) {
				
					//Return the help content with an <h5> title
					$help = "<div class='su-help'>\n";
					$help .= '<h5>'.sprintf(__('%s Help', 'seo-ultimate'),
						$this->modules[$key]->get_page_title())."</h5>\n";
					$help .= "<div class='metabox-prefs'>\n".$customhelp."\n</div>\n";
					$help .= "</div>\n";
					return $help;
				}
			}
		} elseif (strcmp($screen, 'post') == 0 || strcmp($screen, 'page') == 0) {
		
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
		
		global $pagenow;
		
		if ($pagenow == 'plugins.php') {
		
			$r_plugins = array(
				//  'all-in-one-seo-pack/all_in_one_seo_pack.php'
				//, 
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
		if ($context == 'active') {
			echo "<tr><td colspan='5' class='su-plugin-notice plugin-update'>\n";
			printf(__('SEO Ultimate includes the functionality of %1$s. You may want to deactivate %1$s to avoid plugin conflicts.', 'seo-ultimate'), $data['Name']);
			echo "</td></tr>\n";
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
			$info = $this->load_webpage("http://www.seodesignsolutions.com/apis/su/update-info/?ov=".urlencode(SU_VERSION)."&amp;nv=".urlencode($r->new_version));
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
		
		//Compile the fields array
		$fields = array();
		foreach ($this->modules as $module)
			$fields = $module->postmeta_fields($fields, $screen);
		
		if (count($fields) > 0) {
		
			//Sort the fields array
			ksort($fields, SORT_STRING);
			
			//Return a string
			return implode("\n", $fields);
		}
		
		return '';
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
	 * @return int The ID of the post being saved.
	 */
	function save_postmeta_box($post_id, $post) {
		
		//Sanitize
		$post_id = (int)$post_id;
		
		//Don't save postmeta if this is a revision!
		if ($post->post_type == 'revision') return;
		
		//Run preliminary permissions checks
		if ( !check_admin_referer('su-update-postmeta', '_su_wpnonce') ) return;
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id )) return;
		} elseif ( 'post' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_post', $post_id )) return;
		} else return;
		
		//Get an array of all postmeta
		$allmeta = wp_cache_get($post_id, 'post_meta');
		if (!$allmeta) {
			update_postmeta_cache($post_id);
			$allmeta = wp_cache_get($post_id, 'post_meta');
		}
		
		//Update postmeta values
		foreach ($_POST as $key => $value) {
			if (substr($key, 0, 4) == '_su_') {
				
				//Turn checkboxes into integers
				if (strcmp($value, '1') == 0) $value = 1;
				
				//Set the postmeta
				update_post_meta($post_id, $key, $value);
				
				//This value has been updated.
				unset($allmeta[$key]);
			}
		}
		
		//Update values for unchecked checkboxes.
		foreach ($allmeta as $key => $value) {
			if (substr($key, 0, 4) == '_su_') {
				$value = maybe_unserialize($value[0]);
				if ($value == 1)
					update_post_meta($post_id, $key, 0);
			}
		}
		
		//All done
		return $post_id;
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
		$crondata = maybe_unserialize(get_option('su_cron'));
		if (is_array($crondata)) {
			$newcrondata = $crondata;
			
			foreach ($crondata as $key => $crons) {
				if ($remove_all || !isset($this->modules[$key])) {
					foreach ($crons as $data) { wp_clear_scheduled_hook($data[0]); }
					unset($newcrondata[$key]);
				}
			}
			
			update_option('su_cron', serialize($newcrondata));
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
}

/**
 * The pseudo-abstract class upon which all modules are based
 * 
 * @abstract
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
	 * @return mixed The help text, or false if no custom help is available.
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
	
	
	/********** INITIALIZATION FUNCTION **********/
	
	/**
	 * Runs preliminary initialization tasks before calling the module's own init() function.
	 * 
	 * @since 0.1
	 * @uses get_default_settings()
	 * @uses get_setting()
	 * @uses update_setting()
	 * @uses init()
	 */
	function init_pre() {
		$defaults = $this->get_default_settings();
		foreach ($defaults as $setting => $default) {
			if ($this->get_setting($setting, "<reset>") === "<reset>")
				$this->update_setting($setting, $default);
		}
		
		$this->init();
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
	
	
	/********** SETTINGS FUNCTIONS **********/
	
	/**
	 * Retrieves the given setting from a module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * 
	 * @param string $key The name of the setting to retrieve.
	 * @param string $default What should be returned if the setting does not exist. Optional.
	 * @param string $module The module to which the setting belongs. Defaults to the current module. Optional.
	 * @return mixed The value of the setting, or the $default variable.
	 */
	function get_setting($key, $default=null, $module=null) {
		if (!$module) $module = $this->get_module_key();
		$settings = maybe_unserialize(get_option('su_settings'));
		if (isset($settings[$module][$key]))
			return $settings[$module][$key];
		else
			return $default;
	}
	
	/**
	 * Sets a value in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * 
	 * @param string $key The key of the setting to be changed.
	 * @param string $value The new value to assign to the setting.
	 */
	function update_setting($key, $value) {
		$settings = maybe_unserialize(get_option('su_settings'));
		if (!$settings) $settings = array();
		$settings[$this->get_module_key()][$key] = $value;
		update_option('su_settings', serialize($settings));
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
	 * 
	 * @param mixed $header The text of the subheader that should go right before the form. Optional.
	 * @param boolean $table Whether or not to start a form table.
	 */
	function admin_form_start($header = false, $table = true) {
		$hook = SEO_Ultimate::key_to_hook($this->get_module_key());
		if ($header) $this->admin_subheader($header);
		if ($this->is_action('update')) $this->queue_message('success', __('Settings updated.', 'seo-ultimate'));
		$this->print_messages();
		echo "<form method='post' action='?page=$hook'>\n";
		settings_fields($hook);
		echo "\n";
		if ($table) echo "<table class='form-table'>\n";
	}
	
	/**
	 * Ends an administration form.
	 * Closes the table tag, outputs a "Save Changes" button, and closes the form tag.
	 * 
	 * @since 0.1
	 * 
	 * @param boolean $table Whether or not a form table should be ended.
	 */
	function admin_form_end($table = true) {
		if ($table) echo "</table>\n";
?>
<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
<?php
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
			$value = attribute_escape($this->get_setting($id));
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
	 */
	function textareas($textareas) {
		
		if ($this->is_action('update')) {
			foreach ($textareas as $id => $title) {
				$this->update_setting($id, stripslashes($_POST[$id]));
			}
		}
		
		foreach ($textareas as $id => $title) {
			register_setting($this->get_module_key(), $id);
			$value = attribute_escape($this->get_setting($id));
			$id = attribute_escape($id);
			
			echo "<tr valign='top'>\n<th scope='row'><label for='$id'>$title</label></th>\n";
			echo "<td><textarea name='$id' id='$id' type='text' class='regular-text' cols='30' rows='3'>$value</textarea>";
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
	 * @return string The HTML that would render the textarea.
	 */
	function textarea($id, $title) {
		$this->textareas(array($id => $title));
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
	 * @uses get_module_key()
	 * @uses SU_PLUGIN_NAME
	 * 
	 * @param string $action The name of the action.
	 * @param mixed $id The ID of the object being acted upon. Optional.
	 * @return The handle to use for the nonce.
	 */
	function get_nonce_handle($action, $id = false) {
		$hook = SEO_Ultimate::key_to_hook($this->get_module_key());
		
		if (strcmp($action, 'update') == 0)
			//We use the settings_fields() function, which outputs a nonce in this particular format.
			return "$hook-options";
		else {
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
	
	
	/********** HITS LOG FUNCTIONS **********/
	
	function hits_table($where = false, $actions_callback = false) {
		global $wpdb;
		$mk = $this->get_module_key();
		
		$table = SEO_Ultimate::get_table_name('hits');
		if ($where) $where = " WHERE $where";
		$result = $wpdb->get_results("SELECT * FROM $table$where ORDER BY id DESC", ARRAY_A);
		
		if (!$result) return false;
		
		$allfields = array(
			  'time' => __("Date", 'seo-ultimate')
			, 'ip_address' => __("IP Address", 'seo-ultimate')
			, 'user_agent' => __("Browser", 'seo-ultimate')
			, 'url' => __("URL", 'seo-ultimate')
			, 'status_code' => __("Status Code", 'seo-ultimate')
		);
		
		$fields = array();
		
		foreach ($allfields as $col => $title) {
			if (strpos($where, $col) === false) $fields[$col] = $title;
		}
		
		//if ($actions_callback) $fields['actions'] = __("Actions", 'seo-ultimate');
		
		echo "<table class='widefat' cellspacing='0'>\n\t<thead><tr>\n";
		
		foreach ($fields as $title) {
			$class = str_replace(' ', '-', strtolower($title));
			echo "\t\t<th scope='col' class='hit-$class'>$title</th>\n";
		}
		
		echo "\t</tr></thead>\n\t<tbody>\n";
		
		foreach ($result as $row) {
			
			if ($row['is_new']) $class = ' class="new-hit"'; else $class='';
			echo "\t\t<tr$class>\n";
			
			foreach ($fields as $col => $title) {
				$cell = htmlspecialchars($row[$col]);
				
				switch ($col) {
					case 'time':
						$date = date_i18n(get_option('date_format'), $cell);
						$time = date_i18n(get_option('time_format'), $cell);
						$cell = sprintf(__('%1$s<br />%2$s', 'seo-ultimate'), $date, $time);
						break;
					case 'user_agent':
						$cell = get_browser($cell)->parent;
						break;
					case 'url':
						if ($actions_callback) {
							$actions = call_user_func(array($this, $actions_callback), $row);
							$actions = apply_filters("su_hits_table_actions-$mk", $actions, $row);
							$cell = $this->hover_row($cell, $actions);
						}
						break;
				}
				
				$class = str_replace(' ', '-', strtolower($title));
				echo "\t\t\t<td class='hit-$class'>$cell</td>\n";
			}
			echo "\t\t</tr>\n";
			
			$wpdb->update($table, array('is_new' => 0), array('id' => $row['id']));
			//$wpdb->query($wpdb->prepare("UPDATE $table SET is_new=0 WHERE id = %d", $row['id']));
		}
		
		echo "\t</tbody>\n</table>\n";
		
		return true;
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
	
	/**
	 * Loads a webpage and returns its HTML as a string.
	 * 
	 * @since 0.1
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
}

class SU_Widget {
	
	function get_title()   { return ''; }
	function get_section() { return 'normal'; }
	function get_priority(){ return 'core'; }
	
	function content() { }
}


/********** CONSTANTS **********/

define('SU_MODULE_ENABLED', 10);
define('SU_MODULE_SILENCED', 5);
define('SU_MODULE_HIDDEN', 0);
define('SU_MODULE_DISABLED', -10);

define('SU_RESULT_OK', 1);
define('SU_RESULT_WARNING', 0);
define('SU_RESULT_ERROR', -1);


/********** INDEPENDENTLY-OPERABLE FUNCTIONS **********/

/**
 * Returns the plugin's User-Agent value.
 * Can be used as a WordPress filter.
 * 
 * @since 0.1
 * @uses SU_USER_AGENT
 * 
 * @return string The user agent.
 */
function su_get_user_agent() {
	return SU_USER_AGENT;
}

/**
 * Records an event in the debug log file.
 * Usage: su_debug_log(__FILE__, __CLASS__, __FUNCTION__, __LINE__, "Message");
 * 
 * @since 0.1
 * @uses SU_VERSION
 * 
 * @param string $file The value of __FILE__
 * @param string $class The value of __CLASS__
 * @param string $function The value of __FUNCTION__
 * @param string $line The value of __LINE__
 * @param string $message The message to log.
 */
function su_debug_log($file, $class, $function, $line, $message) {
	global $seo_ultimate;
	if (isset($seo_ultimate->modules['settings']) && $seo_ultimate->modules['settings']->get_setting('debug_mode') === true) {
	
		$date = date("Y-m-d H:i:s");
		$version = SU_VERSION;
		$message = str_replace("\r\n", "\n", $message);
		$message = str_replace("\n", "\r\n", $message);
		
		$log = "Date: $date\r\nVersion: $version\r\nFile: $file\r\nClass: $class\r\nFunction: $function\r\nLine: $line\r\nMessage: $message\r\n\r\n";
		$logfile = trailingslashit(dirname(__FILE__))."seo-ultimate.log";
		
		@error_log($log, 3, $logfile);
	}
}


/********** CLASS FUNCTION ALIASES **********/

/**
 * Launches the uninstallation process.
 * WordPress will call this when the plugin is uninstalled, as instructed by the register_uninstall_hook() call in {@link SEO_Ultimate::__construct()}.
 * 
 * @since 0.1
 * @uses $seo_ultimate
 * @uses SEO_Ultimate::uninstall()
 */
function su_uninstall() {
	global $seo_ultimate;
	$seo_ultimate->uninstall();
}


/********** PLUGIN FILE LOAD HANDLER **********/

//If we're running WordPress, then initialize the main class defined above.
//Show CSS or JavaScript if requested.
//Or, show a blank page on direct load.

global $seo_ultimate;
if (defined('ABSPATH'))
	$seo_ultimate = new SEO_Ultimate();
else {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

?>