<?php
/**
 * Non-class functions.
 */

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

/**
 * Returns whether or not a given string starts with a given substring.
 * 
 * @since 0.4
 * 
 * @param string $str The "haystack" string.
 * @param string $sub The "needle" string.
 * @return bool Whether or not $str starts with $sub.
 */
function su_str_startswith( $str, $sub ) {
   return ( substr( $str, 0, strlen( $sub ) ) === $sub );
}

/**
 * Returns whether or not a given string ends with a given substring.
 * 
 * @since 1.0
 * 
 * @param string $str The "haystack" string.
 * @param string $sub The "needle" string.
 * @return bool Whether or not $str ends with $sub.
 */
function su_str_endswith( $str, $sub ) {
   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
}

/**
 * Truncates a string if it is longer than a given length.
 * 
 * @since 0.8
 * 
 * @param string $str The string to possibly truncate.
 * @param int $maxlen The desired maximum length of the string.
 * @param str $truncate The string that should be added to the end of a truncated string.
 */
function su_str_truncate( $str, $maxlen, $truncate = '...' ) {
	if ( strlen($str) > $maxlen )
		return substr( $str, 0, $maxlen - strlen($truncate) ) . $truncate;
	
	return $str;
}

/**
 * Escapes an attribute value and removes unwanted characters.
 * 
 * @since 0.8
 * 
 * @param string $str The attribute value.
 * @return string The filtered attribute value.
 */
function su_esc_attr($str) {
	$str = str_replace(array("\t", "\r\n", "\n"), ' ', $str);
	$str = attribute_escape($str);
	return $str;
}

/**
 * Joins strings into a natural-language list.
 * Can be internationalized with gettext or the su_lang_implode filter.
 * 
 * @since 1.1
 * 
 * @param array $items The strings (or objects with $var child strings) to join.
 * @param string|false $var The name of the items' object variables whose values should be imploded into a list.
	If false, the items themselves will be used.
 * @param bool $ucwords Whether or not to capitalize the first letter of every word in the list.
 * @return string|array The items in a natural-language list.
 */
function su_lang_implode($items, $var=false, $ucwords=false) {
	
	if (is_array($items) ) {
		
		if (strlen($var)) {
			$_items = array();
			foreach ($items as $item) $_items[] = $item->$var;
			$items = $_items;
		}
		
		if ($ucwords) $items = array_map('ucwords', $items);
		
		switch (count($items)) {
			case 0: $list = ''; break;
			case 1: $list = $items[0]; break;
			case 2: $list = sprintf(__('%s and %s', 'seo-ultimate'), $items[0], $items[1]); break;
			default:
				$last = array_pop($items);
				$list = implode(__(', ', 'seo-ultimate'), $items);
				$list = sprintf(__('%s, and %s', 'seo-ultimate'), $list, $last);
				break;
		}
		
		return apply_filters('su_lang_implode', $list, $items);
	}

	return $items;
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

/********** DROPDOWN CODE **********/

//Special thanks to the Drafts Dropdown plugin for the abstracted code
//http://alexking.org/projects/wordpress

if (!function_exists('screen_meta_html')) {

function screen_meta_html($meta) {
	extract($meta);
	if (function_exists($content)) {
		$content = $content();
	}
	echo '
<div id="screen-meta-'.$key.'-wrap" class="screen-meta-wrap hidden">
	<div class="screen-meta-content">'.$content.'</div>
</div>
<div id="screen-meta-'.$key.'-link-wrap" class="hide-if-no-js screen-meta-toggle cf">
<a href="#screen-meta-'.$key.'-wrap" id="screen-meta-'.$key.'-link" class="show-settings">'.$label.'</a>
</div>
	';
}

}

if (!function_exists('screen_meta_output')) {

function screen_meta_output() {
	global $screen_meta;
/*
expected format:
$screen_meta = array(
	array(
		'key' => 'drafts',
		'label' => 'Drafts',
		'content' => 'screen_meta_drafts_content' // can be content or function name
	)
);
*/
	if (!$screen_meta) $screen_meta = array();
	$screen_meta = apply_filters('screen_meta', $screen_meta);
	echo '<div id="screen-meta-extra-content">';
	foreach ($screen_meta as $meta) {
		screen_meta_html($meta);
	}
	echo '</div>';
?>
<style type="text/css">
.screen-meta-toggle {
	float: right;
	background: transparent url( <?php bloginfo('wpurl'); ?>/wp-admin/images/screen-options-left.gif ) no-repeat 0 0;
	font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
	height: 22px;
	padding: 0;
	margin: 0 6px 0 0;
}
.screen-meta-wrap h5 {
	margin: 8px 0;
	font-size: 13px;
}
.screen-meta-wrap {
	border-style: none solid solid;
	border-top: 0 none;
	border-width: 0 1px 1px;
	margin: 0 15px;
	padding: 8px 12px 12px;
	-moz-border-radius: 0 0 0 4px;
	-webkit-border-bottom-left-radius: 4px;
	-khtml-border-bottom-left-radius: 4px;
	border-bottom-left-radius: 4px;
}
</style>
<script type="text/javascript">
jQuery(function($) {

// These hacks not needed if adopted into core
// move tabs into place
	$('#screen-meta-extra-content .screen-meta-toggle.cf').each(function() {
		$('#screen-meta-links').append($(this));
	});
// Move content into place
	$('#screen-meta-extra-content .screen-meta-wrap').each(function() {
		$('#screen-meta-links').before($(this));
	});
// end hacks

// simplified generic code to handle all screen meta tabs
	$('#screen-meta-links a.show-settings').unbind().click(function() {
		var link = $(this);
		$(link.attr('href')).slideToggle('fast', function() {
			if (link.hasClass('screen-meta-shown')) {
				link.css({'backgroundImage':'url("images/screen-options-right.gif")'}).removeClass('screen-meta-shown');
				$('.screen-meta-toggle').css('visibility', 'visible');
			}
			else {
				$('.screen-meta-toggle').css('visibility', 'hidden');
				link.css({'backgroundImage':'url("images/screen-options-right-up.gif")'}).addClass('screen-meta-shown').parent().css('visibility', 'visible');
			}
		});
		return false;
	});
	
	var copy = $('#contextual-help-wrap');
	$('.screen-meta-wrap').css({
		'background-color': copy.css('background-color'),
		'border-color': copy.css('border-bottom-color')
	});
	
});
</script>

<?php
}
add_action('admin_footer', 'screen_meta_output');

}

?>