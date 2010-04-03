<?php
/*
JLFunctions IO Class
Copyright (c)2009-2010 John Lamansky
*/

class suio {

	function is_file($filename, $path, $ext=false) {
		$is_ext = strlen($ext) ? sustr::endswith($filename, '.'.ltrim($ext, '*.')) : true;		
		return is_file(suio::tslash($path).$filename) && $is_ext;
	}
	
	function is_dir($name, $path) {
		return $name != '.' && $name != '..' && is_dir(suio::tslash($path).$name);
	}
	
	function tslash($path) {
		return suio::untslash($path).'/';
	}
	
	function untslash($path) {
		return rtrim($path, '/');
	}
	
}

?>