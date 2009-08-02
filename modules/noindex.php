<?php
/**
 * Noindex Manager Module
 * 
 * @version 1.1
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_Noindex extends SU_Module {

	function get_menu_title() { return __('Noindex Manager', 'seo-ultimate'); }
	
	function init() {
		
		//Hook into our wp_head() action
		add_action('su_meta_robots', array($this, 'wphead_noindex'), 1);
		
		//Now we'll hook into places where wp_head() is not called
		
		//Hook into comment feed headers
		if ($this->get_setting('noindex_comments_feed'))
			add_action('commentsrss2_head', array($this, 'rss2_noindex_tag'));
		
		//Hook into the admin header
		if ($this->get_setting('noindex_admin'))
			add_action('admin_head', array($this, 'xhtml_noindex_tag'));
		
		//Hook into the login header
		if ($this->get_setting('noindex_login'))
			add_action('login_head', array($this, 'xhtml_noindex_tag'));
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
	
	function admin_dropdowns() {
		return array(
			  'overview' => __('Overview', 'seo-ultimate')
			, 'settings' => __('Settings Help', 'seo-ultimate')
		);
	}
	
	function admin_dropdown_overview() {
		return __("
<ul>
	<li><p><strong>What it does:</strong> Noindex Manager lets you prohibit the search engine spiders from indexing certain pages on your blog using the &quot;meta robots noindex&quot; tag.</p></li>
	<li><p><strong>Why it helps:</strong> This module lets you &#8220;noindex&#8221; pages that contain unimportant content (e.g. the login page), or pages that mostly contain duplicate content.</p></li>
	<li><p><strong>How to use it:</strong> Adjust the settings as desired, and then click Save Changes. You can refer to the &#8220;Settings Help&#8221; tab for information on the settings available.</p></li>
</ul>
", 'seo-ultimate');
	}
	
	function admin_dropdown_settings() {
		return __("
<p>Here&#8217;s information on the various settings:</p>
<ul>
	<li><p><strong>Administration back-end pages</strong> &mdash; Tells spiders not to index the administration area (the part you&#8217;re in now),
		in the unlikely event a spider somehow gains access to the administration. Recommended.</p></li>
	<li><p><strong>Author archives</strong> &mdash; Tells spiders not to index author archives. Useful if your blog only has one author.</p></li>
	<li><p><strong>Blog search pages</strong> &mdash; Tells spiders not to index the result pages of WordPress&#8217;s blog search function. Recommended.</p></li>
	<li><p><strong>Category archives</strong> &mdash; Tells spiders not to index category archives. Recommended only if you don&#8217;t use categories.</p></li>
	<li><p><strong>Comment feeds</strong> &mdash; Tells spiders not to index the RSS feeds that exist for every post&#8217;s comments.
		(These comment feeds are totally separate from your normal blog feeds.) Recommended.</p></li>
	<li><p><strong>Comment subpages</strong> &mdash; Tells spiders not to index posts' comment subpages.</p></li>
	<li><p><strong>Date-based archives</strong> &mdash; Tells spiders not to index day/month/year archives.
		Recommended, since these pages have little keyword value.</p></li>
	<li><p><strong>Subpages of the homepage</strong> &mdash; Tells spiders not to index the homepage's subpages (page 2, page 3, etc).
		Recommended.</p></li>
	<li><p><strong>Tag archives</strong> &mdash; Tells spiders not to index tag archives. Recommended only if you don&#8217;t use tags.</p></li>
	<li><p><strong>User login/registration pages</strong> &mdash; Tells spiders not to index WordPress&#8217;s user login and registration pages. Recommended.</p></li>
</ul>
", 'seo-ultimate');
	}
}

}
?>