<?php
/**
 * AISOP Import Module
 * 
 * @version 1.0
 * @since 1.6
 */

if (class_exists('SU_ImportModule')) {

class SU_ImportAIOSP extends SU_ImportModule {
	
	function get_module_title() { return __('Import from All in One SEO Pack', 'seo-ultimate'); }
	function get_menu_title() { return __('AIOSP Import', 'seo-ultimate'); }
	
	function get_op_title() { return __('All in One SEO Pack', 'seo-ultimate'); }
	function get_import_desc() { return __("Import post data (custom title tags and meta tags).", 'seo-ultimate'); }
	
	function get_default_settings() {
		return array(
			  'import_postmeta' => true
			, 'postmeta_bothexist_action' => 'skip'
			, 'after_post_import' => 'nothing'
		);
	}
	
	function admin_page_contents() {
		echo "<p>";
		_e("Here you can move post fields from the All in One SEO Pack (AIOSP) plugin to SEO Ultimate. AIOSP&#8217;s data remains in your WordPress database after AIOSP is deactivated or even uninstalled. This means that as long as AIOSP was active on this blog sometime in the past, AIOSP does <em>not</em> need to be currently installed or activated for the import to take place.", 'seo-ultimate');
		echo "</p>\n<p>";
		_e("The import tool can only move over data from AIOSP version 1.6 or above. If you use an older version of AIOSP, you should update to the latest version first and run AIOSP&#8217;s upgrade process.", 'seo-ultimate');
		echo "</p>\n";
		
		$this->admin_form_start();
		
		$this->textblock('<strong>'.__('Import Post Fields', 'seo-ultimate').'</strong> &mdash; '.
			__("Post fields store the SEO data for your posts/pages (i.e. your custom title tags, meta descriptions, and meta keywords). If you provided custom titles/descriptions/keywords to All in One SEO Pack, this importer can move that data over to SEO Ultimate.", 'seo-ultimate')
		);
		$this->admin_form_indent_start();
		$this->admin_form_group_start(__('Conflict Resolution Mode', 'seo-ultimate'));
		$this->textblock(__("What should the import tool do if it tries to move over a post&#8217;s AIOSP data, but different data already exists in the corresponding SEO Ultimate fields?", 'seo-ultimate'));
		$this->radiobuttons('postmeta_bothexist_action', array(
			  'skip' => __("Skip that post and leave all data as-is (default).", 'seo-ultimate')
			, 'delete_su' => __("Delete the SEO Ultimate data and replace it with the AIOSP data.", 'seo-ultimate')
			, 'delete_op' => __("Keep the SEO Ultimate data and delete the AIOSP data.", 'seo-ultimate')
		));
		$this->admin_form_group_end();
		$this->admin_form_group_start(__('Deletion Preference', 'seo-ultimate'));
		$this->textblock(__("When the migration tool successfully copies a post&#8217;s AIOSP data over to SEO Ultimate, what should it do with the old AIOSP data?", 'seo-ultimate'));
		$this->radiobuttons('after_post_import', array(
			  'delete_op' => __("Delete the AIOSP data.", 'seo-ultimate')
			, 'nothing' => __("Leave behind the duplicate AIOSP data (default).", 'seo-ultimate')
		));
		$this->admin_form_group_end();
		$this->admin_form_indent_end();
		
		$this->admin_form_end();
	}
	
	function do_import() {
		
		if (is_plugin_active(SU_AIOSP_PATH)) {
			deactivate_plugins(SU_AIOSP_PATH);
			$this->import_status('success', __('Deactivated All in One SEO Pack.', 'seo-ultimate'));
		}
		
		/*if (!$this->get_setting('import_postmeta') && !$this->get_setting('import_settings')) {
			$this->import_status('warning', __("No import options selected.", 'seo-ultimate'));
			return;
		}*/
		
		//if ($this->get_setting('import_postmeta')) {
		if (true) {
			global $wpdb;
			$posts = $wpdb->get_results("SELECT `ID` FROM {$wpdb->posts}");
			
			$postmeta_fields = suarr::aprintf('_aioseop_%s', '_su_%s', array('title', 'description', 'keywords'));
			
			$numposts = 0;
			$numfields = 0;
			$numsudels = 0;
			$numopdels = 0;
			
			foreach ($posts as $p) {
				
				//Skip posts with "disabled" AIOSP data
				if (get_post_meta($p->ID, '_aioseop_disable', true) === 'on')
					$numskipped++;
				else {
				
					foreach ($postmeta_fields as $aiosp_field => $su_field) {
						
						if (strlen($aiosp_value = get_post_meta($p->ID, $aiosp_field, true))) {
							
							$delete_op = false;
							
							if (strlen(get_post_meta($p->ID, $su_field, true))) {
								//Conflict: SEO Ultimate field already exists
								
								switch ($this->get_setting('postmeta_bothexist_action')) {
									case 'skip': continue 2; break;
									case 'delete_su': $numsudels++; break;
									case 'delete_op': $delete_op = true; break;
								}
							}
							
							//Import the AIOSP data if we're not supposed to delete it.
							if (!$delete_op)
								update_post_meta($p->ID, $su_field, $aiosp_value);
							
							//Delete the AIOSP data if the user has instructed us to do so
							if ($delete_op || $this->get_setting('after_post_import') == 'delete_op') {
								delete_post_meta($p->ID, $aiosp_field, $aiosp_value);
								$numopdels++;
							}
							
							$numfields++;
						}
					}
				}
				
				$numposts++;
			}
			
			$this->import_status('success', sprintf(_n(
				'Imported a total of %d fields for one post/page/revision.',
				'Imported a total of %1$d fields for %2$d posts/pages/revisions.',
				$numposts, 'seo-ultimate'), $numfields, $numposts));
			
			if ($numskipped > 0)
				$this->import_status('info', sprintf(_n(
					'Skipped one post with disabled AIOSP data.',
					'Skipped %d posts with disabled AIOSP data.',
					$numskipped, 'seo-ultimate'), $numskipped));
			
			if ($numsudels > 0)
				$this->import_status('info', sprintf(_n(
					'Overwrote one SEO Ultimate field with AIOSP data, as instructed by the settings you chose.',
					'Overwrote %d SEO Ultimate fields with AIOSP data, as instructed by the settings you chose.',
					$numsudels, 'seo-ultimate'), $numsudels));
			
			if ($numopdels > 0)
				$this->import_status('info', sprintf(_n(
					'Deleted one AIOSP field, as instructed by the settings you chose.',
					'Deleted %d AIOSP fields, as instructed by the settings you chose.',
					$numopdels, 'seo-ultimate'), $numopdels));
		}
	}
}

} elseif (strcmp($_GET['css'], 'admin') == 0) {
	header('Content-type: text/css');
?>

#su-import-aiosp tr.su-admin-form-checkbox td,
#su-import-aiosp tr.su-admin-form-textblock td {
	border-top: 3px solid #ccc;
	padding-top: 1em;
}

#su-import-aiosp table.form-table {
	border-bottom: 3px solid #ccc;
}

#su-import-aiosp td table tr.su-admin-form-checkbox td,
#su-import-aiosp td table tr.su-admin-form-textblock td {
	border-top: 0 none;
	padding-top: 0;
	padding-bottom: 0;
}

<?php
}
?>