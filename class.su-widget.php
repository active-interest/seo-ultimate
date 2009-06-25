<?php
/**
 * The pseudo-abstract class upon which all widgets are based.
 * 
 * @abstract
 * @version 1.0
 * @since 0.1
 */
class SU_Widget {
	
	function get_title()   { return ''; }
	function get_section() { return 'normal'; }
	function get_priority(){ return 'core'; }
	
	function content() { }
}
?>