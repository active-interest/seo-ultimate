<?php
/**
 * Stats Central Module
 * 
 * @version 0.1.2
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_Stats extends SU_Module {

	function get_page_title() { return __('Modules', 'seo-ultimate'); } //SEO Stats Central
	function get_menu_title() { return __('Modules', 'seo-ultimate'); } //Stats Central
	function get_menu_pos()   { return 10; }

	function admin_page_contents() {
		echo "<ul>";
		global $seo_ultimate;
		foreach ($seo_ultimate->modules as $module) {
			if ($module->get_menu_parent() == 'seo' && $module->get_parent_module() === false) {
				$key = $module->get_module_key();
				if ($key != $this->get_module_key()) {
					$key = SEO_Ultimate::key_to_hook($key);
					$title = $module->get_menu_title();
					echo "<li><a href='admin.php?page=$key'>$title</a></li>\n";
				}
			}
		}
		echo "</ul>";
	}
	
	function admin_help() {
		return __(<<<STR
<p>Click a module&#8217;s name to open its admin page.</p>
STR
, 'seo-ultimate');
	}

}

} elseif ($_GET['css'] == 'admin') {
	header('Content-type: text/css');
?>

#su-stats ul {
	margin: 1em 0;
	list-style-type: disc;
	padding-left: 1em;
}

<?php
}
?>