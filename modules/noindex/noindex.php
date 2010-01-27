<?php
/**
 * Noindex Manager Module
 * 
 * @version 1.1.2
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_Noindex extends SU_Module {

	function get_module_title() { return __('Noindex Manager', 'seo-ultimate'); }
	
	function init() {
		
		//Hook into our wp_head() action
		add_action('su_meta_robots', array(&$this, 'wphead_noindex'), 1);
		
		//Now we'll hook into places where wp_head() is not called
		
		//Hook into comment feed headers
		if ($this->get_setting('noindex_comments_feed'))
			add_action('commentsrss2_head', array(&$this, 'rss2_noindex_tag'));
		
		//Hook into the admin header
		if ($this->get_setting('noindex_admin'))
			add_action('admin_head', array(&$this, 'xhtml_noindex_tag'));
		
		//Hook into the login header
		if ($this->get_setting('noindex_login'))
			add_action('login_head', array(&$this, 'xhtml_noindex_tag'));
	}
	
	function admin_page_contents() {
		
		//If global noindex tags are enabled, these settings will be moot, so notify the user.
		if (!get_option('blog_public'))
			$this->queue_message('error',
				__("Note: The current <a href='options-privacy.php'>privacy settings</a> will block indexing of the entire site, regardless of which options are set below.", 'seo-ultimate') );
		
		$this->admin_form_start();
		$this->admin_form_subheader(__('Prevent indexing of...', 'seo-ultimate'));
		$this->checkboxes(array('noindex_admin' => __('Administration back-end pages', 'seo-ultimate')
							,	'noindex_author' => __('Author archives', 'seo-ultimate')
							,	'noindex_search' => __('Blog search pages', 'seo-ultimate')
							,	'noindex_category' => __('Category archives', 'seo-ultimate')
							,	'noindex_comments_feed' => __('Comment feeds', 'seo-ultimate')
							,	'noindex_cpage' => __('Comment subpages', 'seo-ultimate')
							,	'noindex_date' => __('Date-based archives', 'seo-ultimate')
							,	'noindex_home_paged' => __('Subpages of the homepage', 'seo-ultimate')
							,	'noindex_tag' => __('Tag archives', 'seo-ultimate')
							,	'noindex_login' => __('User login/registration pages', 'seo-ultimate')
		));
		$this->admin_form_end();
	}
	
	function wphead_noindex($commands) {
	
		if ($this->should_noindex())
			array_push($commands, 'noindex', 'nofollow');
			
		return $commands;
	}
	
	function should_noindex() {
	
		$checks = array('author', 'search', 'category', 'date', 'tag');
	
		foreach ($checks as $setting) {
			if (call_user_func("is_$setting")) return $this->get_setting("noindex_$setting");
		}
		
		//Homepage subpages
		if ($this->get_setting('noindex_home_paged') && is_home() && is_paged()) return true;
		
		//Comment subpages
		global $wp_query;
		if ($this->get_setting('noindex_cpage') && isset($wp_query->query_vars['cpage'])) return true;
		
		return false;
	}
	
	function rss2_noindex_tag() {
		echo "<xhtml:meta xmlns:xhtml=\"http://www.w3.org/1999/xhtml\" name=\"robots\" content=\"noindex\" />\n";
	}
	
	function xhtml_noindex_tag() {
		echo "\t<meta name=\"robots\" content=\"noindex\" />\n";
	}
}

}
?>