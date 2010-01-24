<?php
/**
 * 404 Monitor Module
 * 
 * @version 1.0.8
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
	
	function get_module_title() { return __('404 Monitor', 'seo-ultimate'); }
	
	function has_menu_count() { return true; }
	
	function get_menu_count() {
		//Find out how many *new* 404s there are
		global $wpdb;
		$table = $this->plugin->get_table_name('hits');
		return $wpdb->query("SELECT id FROM $table WHERE is_new=1 AND status_code=404 AND redirect_url='' AND url NOT LIKE '%/favicon.ico'");
	}
	
	function admin_page_contents() {
		
		global $wpdb;
		$table = $this->plugin->get_table_name('hits');
		
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
}

}
?>