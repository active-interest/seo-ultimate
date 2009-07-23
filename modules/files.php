<?php
/**
 * File Editor Module
 * 
 * @version 1.0
 * @since 2.0
 */

if (class_exists('SU_Module')) {

class SU_Files extends SU_Module {

	var $htaccess_recovery = null;
	
	function get_menu_title() { return __('File Editor', 'seo-ultimate'); }
	
	function init() {
		
		if ($this->get_setting('enable_custom_robotstxt')) {
			remove_action('do_robots', 'do_robots');
			add_action('do_robots', array($this, 'do_robots'));
		}
		
		add_action('admin_notices', array($this, 'privacy_options_notice'));
		
		add_filter('su_get_setting-files-htaccess', array($this, 'get_htaccess'));
		add_filter('su_custom_update_setting-files-htaccess', array($this, 'update_htaccess'), 10, 2);
	}
	
	function admin_page_contents() {
		
		global $is_apache;
		
		if ($is_apache) {
			
			$htaccess = get_home_path().'.htaccess';
			$exists = file_exists($htaccess);
			$writable = is_writable($htaccess);
			
			if ($exists && !$writable) $this->queue_message('warning',
				__('A .htaccess file exists, but it&#8217;s not writable. You can edit it here once the file permissions are corrected.', 'seo-ultimate'));
		}
		
		if (!strlen(get_option('permalink_structure'))) $this->queue_message('error',
			__('WordPress won&#8217;t be able to display your robots.txt file because the default <a href="options-permalink.php" target="_blank">permalink structure</a> is in use.', 'seo-ultimate'));
		
		$this->print_messages();
		
		$this->admin_form_start();
		
		$this->textarea('robotstxt', sprintf(__('robots.txt [<a href="%s" target="_blank">Open</a>]', 'seo-ultimate'), trailingslashit(get_bloginfo('url')).'robots.txt'));
		$this->checkboxes(array(
			  'enable_custom_robotstxt' => __('Enable this custom robots.txt file and disable the default file', 'seo-ultimate')
			, 'enable_do_robotstxt_action' => __('Let other plugins add rules to my custom robots.txt file', 'seo-ultimate')
		), __('robots.txt Settings', 'seo-ultimate'));
		
		$this->queue_message('warning',
			__('Please realize that incorrectly editing your robots.txt file could block search engines from your site.', 'seo-ultimate'));
		
		if ($is_apache && ($writable || !$exists)) {
			$this->textarea('htaccess', __('.htaccess', 'seo-ultimate'));
			
			$this->queue_message('warning',
				__('Also, incorrectly editing your .htaccess file could disable your entire website. Edit with caution!', 'seo-ultimate'));
		}
		
		$this->admin_form_end();
		$this->print_messages();
	}
	
	function do_robots() {
		header( 'Content-Type: text/plain; charset=utf-8' );
		
		if ($this->get_setting('enable_do_robotstxt_action'))
			do_action('do_robotstxt');
		
		echo $this->get_setting('robotstxt');
	}
	
	function get_htaccess() {
		if ($this->htaccess_recovery) return $this->htaccess_recovery;
		
		$htaccess = get_home_path().'.htaccess';
		if (is_readable($htaccess))
			return file_get_contents($htaccess);
		
		return false;
	}
	
	function update_htaccess($unused, $value) {
		
		$this->htaccess_recovery = $value;
		
		$htaccess = get_home_path().'.htaccess';
		$fp = fopen($htaccess, 'w');
		fwrite($fp, $value);
		fclose($fp);
		
		return true;
	}
	
	function privacy_options_notice() {
		global $pagenow;
		if ($this->get_setting('enable_custom_robotstxt') && $pagenow == 'options-privacy.php') {
			echo '<div class="su-module">';
			$this->print_message('info', sprintf(
				__('Please note that your privacy settings won&#8217;t have any effect on your robots.txt file, since you&#8217;re using <a href="%s">a custom one</a>.', 'seo-ultimate'),
				admin_url('admin.php?page='.SEO_Ultimate::key_to_hook($this->get_module_key()))
			));
			echo '</div>';
		}
	}
	
	function admin_help() {
		return __(<<<STR
<p>The File Editor module lets you edit system files that are of SEO value. Edit the files as desired, then click Save Changes. If you create a custom robots.txt file, be sure to enable it with the checkbox.</p>
STR
, 'seo-ultimate');
	}
}

}
?>