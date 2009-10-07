<?php
/**
 * 404 Monitor Module
 * 
 * @version 1.0.7
 * @since 0.4
 */

if (class_exists('SU_Module')) {

class SU_404s extends SU_Module {
	
	var $hitset;
	
	function __construct() {
		//Load 404s from the database
		$this->hitset = new SU_HitSet('404s', "status_code=404 AND redirect_url='' AND url NOT LIKE '%/favicon.ico'");
		
		add_filter('su_save_hit', array(&$this, 'should_log_hit'), 10, 2);
	}
	
	function should_log_hit($should_log, $hit) {
		if ($hit['status_code'] == 404)
			return true;
		else
			return $should_log;
	}
	
	function get_menu_title() { return __('404 Monitor', 'seo-ultimate'); }
	
	function get_menu_count() {
		//Find out how many *new* 404s there are
		global $wpdb;
		$table = SEO_Ultimate::get_table_name('hits');
		return $wpdb->query("SELECT id FROM $table WHERE is_new=1 AND status_code=404 AND redirect_url='' AND url NOT LIKE '%/favicon.ico'");
	}
	
	function admin_page_contents() {
		
		global $wpdb;
		$table = SEO_Ultimate::get_table_name('hits');
		
		if (!$this->get_setting('log_hits', true, 'settings'))
			
			$this->queue_message('warning', sprintf(
				__('Please note that new 404 errors will not be recorded, since visitor logging is disabled in the %s.', 'seo-ultimate'),
				$this->get_admin_link('settings', __('Plugin Settings module', 'seo-ultimate'))
			));
		
		//Are we deleting a 404 entry?
		if ($this->is_action('delete')) {
			
			if ($wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id = %d LIMIT 1", intval($_GET['object']))))
				$this->queue_message('success', __('The log entry was successfully deleted.', 'seo-ultimate'));
			else
				$this->queue_message('error', __('This log entry has already been deleted.', 'seo-ultimate'));
			
			//The database has changed, so reload our data from it
			$this->hitset->query_db();
			
		//Are we clearing the whole 404 log?
		} elseif ($this->is_action('clear')) {
			
			if ($wpdb->query("DELETE FROM $table WHERE status_code=404")) {
				$this->queue_message('success', __('The log was successfully cleared.', 'seo-ultimate'));
				
				//The database has changed, so reload our data from it
				$this->hitset->query_db();
			}
		}
		
		if (!$this->hitset->have_hits())
			$this->queue_message('success', __("No 404 errors in the log.", 'seo-ultimate'));
		
		$this->print_messages();
		
		if ($this->hitset->have_hits()) {
			
			//Display the 404 table
			$this->hitset->admin_table(array(&$this, 'hits_table_action_links'));
			
			//Create the "Clear Log" button
			$clearurl = $this->get_nonce_url('clear');
			$confirm = __("Are you sure you want to delete all 404 log entries?", 'seo-ultimate');
			echo "<a href=\"$clearurl\" class=\"button-secondary\" onclick=\"javascript:return confirm('$confirm')\">";
			_e("Clear Log", 'seo-ultimate');
			echo "</a>";
		}
	}
	
	//Returns the HTML that should appear when the user hovers over a row of the 404s table
	function hits_table_action_links($row) {
		$url = $row['url'];
		
		$deleteurl = $this->get_nonce_url('delete', $row['id']);
		$url_encoded = urlencode($url);
		
		$anchors = array(
			  __("Open", 'seo-ultimate')
			, __("Google Cache", 'seo-ultimate')
			, __("Delete Log Entry", 'seo-ultimate')
		);
		
		return <<<STR

				<span class="open"><a href="$url" target="_blank">{$anchors[0]}</a> | </span>
				<span class="cache"><a href="http://www.google.com/search?q=cache%3A$url_encoded" target="_blank">{$anchors[1]}</a> | </span>
				<span class="delete"><a href="$deleteurl">{$anchors[2]}</a></span>

STR;
	}
	
	function admin_dropdowns() {
		$dropdowns = array();
		
		//Overview dropdown
		$dropdowns['overview'] = __('Overview', 'seo-ultimate');
		
		//Only show the "Options Help" dropdown if the options in questions are showing
		if ($this->hitset->have_hits()) $dropdowns['options'] = __('Options Help', 'seo-ultimate');
		
		//Troubleshooting dropdown
		$dropdowns['troubleshooting'] = __('Troubleshooting', 'seo-ultimate');
		
		return $dropdowns;
	}
	
	function admin_dropdown_overview() {
		$help = __("
<ul>
	<li><p><strong>What it does:</strong> The 404 Monitor keeps track of non-existant URLs that generated 404 errors.
		404 errors are when a search engine or visitor comes to a URL on your site but nothing exists at that URL.</p></li>
	<li><p><strong>Why it helps:</strong> The 404 Monitor helps you spot 404 errors; 
		then you can take steps to correct them to reduce linkjuice loss from broken links.</p></li>
	<li><p><strong>How to use it:</strong> Check the 404 Monitor occasionally for errors.
		(A numeric bubble will appear next to the &#8220;404 Monitor&#8221; item on the menu if there are any newly-logged URLs that you haven&#8217;t seen yet. 
		These new URLs will also be highlighted green in the table.)
		If a 404 error&#8217;s referring URL is located on your site, try locating and fixing the broken URL.
		If moved content was previously located at the requested URL, try using a redirection plugin to point the old URL to the new one.</p></li>
</ul>
", 'seo-ultimate');
		
		if (!$this->hitset->have_hits()) {
			
			//Only show this if we don't have 404s in the log
			$help .= '<p>'.__('Currently, the 404 Monitor doesn&#8217;t have any 404 errors in its log. This is good, and means there&#8217;s no action required on your part. If the 404 Monitor logs any 404 errors in the future, you&#8217;ll see them on this page.', 'seo-ultimate').'</p>';

		}
		
		return $help;
	}
	
	function admin_dropdown_options() {
		return __("
<p>Hover over a table row to access these options:</p>
<ul>
		<li>The &#8220;View&#8221; link will open the URL in a new window. This is useful for testing whether or not a redirect is working.</li>
		<li>The &#8220;Google Cache&#8221; link will open Google&#8217;s archived version of the URL in a new window. This is useful for determining what content, if any, used to be located at that URL.</li>
		<li>Once you've taken care of a 404 error, you can click the &#8220;Delete Log Entry&#8221; link to remove it from the list. The URL will reappear on the list if it triggers a 404 error in the future.</li>
</ul>
", 'seo-ultimate');
	}
		
	function admin_dropdown_troubleshooting() {
		return sprintf(__("
<p>404 Monitor doesn&#8217;t appear to work? Take these notes into consideration:</p>
<ul>
	<li>Visitor logging must be enabled in the %s. (It&#8217;s enabled by default.)</li>
	<li>In order for the 404 Monitor to track 404 errors, you must have &#8220;Pretty Permalinks&#8221; enabled in your <a href='options-permalink.php'>permalink options</a>.</li>
	<li>Some parts of your website may not be under WordPress&#8217;s control; the 404 Monitor can&#8217;t track 404 errors on non-WordPress website areas.</li>
	<li>The 404 Monitor doesn&#8217;t record 404 errors generated by logged-in users.</li>
</ul>
", 'seo-ultimate'),
			$this->get_admin_link('settings', __('Plugin Settings module', 'seo-ultimate'))
		);
	}
}

}
?>