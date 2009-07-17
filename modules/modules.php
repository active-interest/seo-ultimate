<?php
/**
 * Module Manager Module
 * 
 * @version 1.0
 * @since 0.7
 */

if (class_exists('SU_Module')) {

class SU_Modules extends SU_Module {
	
	function get_menu_title() { return __('Module Manager', 'seo-ultimate'); }
	function get_menu_pos()   { return 10; }
	
	function init() {
		global $seo_ultimate;
		
		if ($_GET['page'] == SEO_Ultimate::key_to_hook($this->get_module_key()) && $this->is_action('update')) {
			
			$module_statuses = maybe_unserialize(get_option('su_modules', false));
			
			foreach ($_POST as $key => $value) {
				if (substr($key, 0, 3) == 'su-') {
					$key = str_replace(array('su-', '-module-status'), '', $key);
					$module_statuses[$key] = intval($value);
				}
			}
			
			$seo_ultimate->module_status = $module_statuses;
			update_option('su_modules', serialize($module_statuses));
		}
	
	}
	
	function admin_page_contents() {
		global $seo_ultimate;
		
		echo "<p>";
		_e("Here you can disable or hide modules you don&#8217;t use. You can also silence modules from displaying bubble alerts on the menu.", 'seo-ultimate');
		echo "</p>";
		
		$this->admin_form_start(false, false);
		
		$headers = array(
			  __("Status", 'seo-ultimate')
			, __("Module", 'seo-ultimate')
		);
		echo <<<STR
<table class="widefat" cellspacing="0">
	<thead><tr>
		<th scope="col" class="module-status">{$headers[0]}</th>
		<th scope="col" class="module-name">{$headers[1]}</th>
	</tr></thead>
	<tbody>

STR;
		
		$statuses = array(
			  SU_MODULE_ENABLED => __('Enabled', 'seo-ultimate')
			, SU_MODULE_SILENCED => __('Silenced', 'seo-ultimate')
			, SU_MODULE_HIDDEN => __('Hidden', 'seo-ultimate')
			, SU_MODULE_DISABLED => __('Disabled', 'seo-ultimate')
		);
		
		$modules = array();
		
		foreach ($seo_ultimate->modules as $key => $module)
			if (get_parent_class($module) == 'SU_Module' && !in_array($key, array('stats', 'modules')))
				$modules[$key] = $module->get_page_title();
		
		$modules = array_merge($modules, $seo_ultimate->disabled_modules);
		asort($modules);
		
		foreach ($modules as $key => $name) {
			
			$currentstatus = $seo_ultimate->module_status[$key];
			
			echo "\t\t<tr>\n\t\t\t<td class='module-status' id='$key-module-status'>\n";
			echo "\t\t\t\t<input type='hidden' name='su-$key-module-status' id='su-$key-module-status' value='$currentstatus' />\n";
			
			foreach ($statuses as $statuscode => $statuslabel) {
				if ($currentstatus == $statuscode) $current = ' current'; else $current = '';
				$codeclass = str_replace('-', 'n', strval($statuscode));
				echo "\t\t\t\t\t<span class='status-$codeclass'>";
				echo "<a href='javascript:void(0)' onclick=\"javascript:set_module_status('$key', $statuscode, this)\" class='$current'>$statuslabel</a></span>\n";
			}
			
			if ($seo_ultimate->module_status[$key] > SU_MODULE_DISABLED) {
				$cellcontent = "<a href='".$seo_ultimate->modules[$key]->get_admin_url()."'>$name</a>";
			} else
				$cellcontent = $name;
			
			echo <<<STR
				</td>
				<td class='module-name'>
					$cellcontent
				</td>
			</tr>

STR;
		}
		
		echo "\t</tbody>\n</table>\n";
		
		$this->admin_form_end(false, false);
	}
}

} elseif ($_GET['css'] == 'admin') {
	header('Content-type: text/css');
?>

#su-modules td.module-status {
	padding-right: 2em;
}

#su-modules td.module-status input {
	display: none;
}

#su-modules td.module-status a {
	float: left;
	display: block;
	border: 1px solid white;
	padding: 0.3em 0.5em;
	color: #999;
	margin-right: 0.2em;
}

#su-modules td.module-status a:hover {
	border-color: #ccc #666 #666 #ccc;
}

#su-modules td.module-status a.current {
	border-color: #666 #ccc #ccc #666;
}

#su-modules td.module-status .status-10 a.current { color: green; }
#su-modules td.module-status .status-5  a.current { color: black; }
#su-modules td.module-status .status-0 a.current  { color: darkorange; }
#su-modules td.module-status .status-n10 a.current{ color: red; }

<?php
} elseif ($_GET['js'] == 'admin') {
	header('Content-type: text/javascript');
?>

function set_module_status(key, input_value, a_obj) {
	var td_id = key+"-module-status";
	var input_id = "su-"+td_id;
	
	jQuery("td#"+td_id+" a").removeClass("current");
	document.getElementById(input_id).value = input_value;
	a_obj.className += " current";
}

<?php
}
?>