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
?>