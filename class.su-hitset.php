<?php

class SU_HitSet {
	
	var $result;
	var $where;
	var $module_key;
	
	function SU_HitSet($mk, $where = false) {
		$this->module_key = $mk;
		if ($where) $where = " WHERE $where";
		$this->where = $where;
		$this->query_db();
	}
	
	function query_db() {
		global $wpdb;
		$table = SEO_Ultimate::get_table_name('hits');
		$this->result = $wpdb->get_results("SELECT * FROM {$table}{$this->where} ORDER BY id DESC", ARRAY_A);
	}
	
	function have_hits() {
		return (is_array($this->result) && count($this->result) > 0);
	}
	
	function admin_table($actions_callback = false, $highlight_new = true) {
		
		if (!$this->result || !$this->where) return;
		
		//Initialize variables
		global $wpdb;
		
		$table = SEO_Ultimate::get_table_name('hits');
		
		$allfields = array(
			  'time' => __("Date", 'seo-ultimate')
			, 'ip_address' => __("IP Address", 'seo-ultimate')
			, 'user_agent' => __("Browser", 'seo-ultimate')
			, 'url' => __("URL Requested", 'seo-ultimate')
			, 'redirect_url' => __("Redirected To", 'seo-ultimate')
			, 'status_code' => __("Status Code", 'seo-ultimate')
		);
		
		$fields = array();
		
		foreach ($allfields as $col => $title) {
			if (strpos($this->where, " $col=") === false) $fields[$col] = $title;
		}
		
		$fields = apply_filters("su_{$this->module_key}_hits_table_columns", $fields);
		
		echo "<table class='widefat' cellspacing='0'>\n\t<thead><tr>\n";
		
		foreach ($fields as $title) {
			$class = str_replace(' ', '-', strtolower($title));
			echo "\t\t<th scope='col' class='hit-$class'>$title</th>\n";
		}
		
		echo "\t</tr></thead>\n\t<tbody>\n";
		
		foreach ($this->result as $row) {
			
			if ($highlight_new && $row['is_new']) $class = ' class="new-hit"'; else $class='';
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
						$binfo = get_browser($cell, true);
						$ua = attribute_escape($cell);
						$cell = '<abbr title="'.$ua.'">'.$binfo['parent'].'</abbr>';
						break;
					case 'url':
						if (is_array($actions_callback)) {
							$actions = call_user_func($actions_callback, $row);
							$actions = apply_filters("su_{$this->module_key}_hits_table_actions", $actions, $row);
							$cell = SU_Module::hover_row($cell, $actions);
						}
						break;
				}
				
				$cell = apply_filters("su_{$this->module_key}_hits_table_{$col}_cell", $cell, $row);
				
				$class = str_replace(' ', '-', strtolower($title));
				echo "\t\t\t<td class='hit-$class'>$cell</td>\n";
			}
			echo "\t\t</tr>\n";
			
			$wpdb->update($table, array('is_new' => 0), array('id' => $row['id']));
		}
		
		echo "\t</tbody>\n</table>\n";
	}

}
?>