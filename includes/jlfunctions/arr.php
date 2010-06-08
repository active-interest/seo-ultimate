<?php
/*
JLFunctions Array Class
Copyright (c)2009-2010 John Lamansky
*/

class suarr {
	
	/**
	 * Plugs an array's keys and/or values into sprintf-style format string(s).
	 * 
	 * @param string|false $keyformat The sprintf-style format for the key, e.g. "prefix_%s" or "%s_suffix"
	 * @param string|false $valueformat The sprintf-style format for the value.
	 * @param array $array The array whose keys/values should be formatted.
	 * @return array The array with the key/value formats applied.
	 */
	function aprintf($keyformat, $valueformat, $array) {
		$newarray = array();
		foreach ($array as $key => $value) {
			if ($keyformat) {
				if (is_int($key)) $key = $value;
				$key = str_replace('%s', $key, $keyformat);
			}
			if ($valueformat) $value = str_replace('%s', $value, $valueformat);
			$newarray[$key] = $value;
		}
		return $newarray;
	}
	
	/**
	 * Removes elements that are blank (after trimming) from the beginning of the given array.
	 */
	function ltrim($array) {
		while (count($array) && !strlen(trim($array[0])))
			array_shift($array);
		return $array;
	}
	
	/**
	 * Removes a value from the array if found.
	 */
	function remove_value(&$array, $value) {
		$index = array_search($value, $array);
		if ($index !== false)
			unset($array[$index]);
	}
	
	/**
	 * Returns whether or not any of the specified $needles are in the $haystack.
	 * 
	 * @param array $needles
	 * @param array $haystack
	 * @param bool $ci Whether or not the search should be case-insensitive.
	 * 
	 * @return bool
	 */
	function any_in_array($needles, $haystack, $ci = false) {
		if ($ci) {
			array_walk($needles, 'strtolower');
			array_walk($haystack, 'strtolower');
		}
		
		foreach ($needles as $needle)
			if (in_array($needle, $haystack)) return true;
		
		return false;
	}
	
	function explode_lines($lines) {
		$lines = explode("\n", $lines);
		array_walk($lines, 'trim'); //Remove any /r's
		return $lines;
	}
	
	//Based on recursive array search function from:
	//http://www.php.net/manual/en/function.array-search.php#91365
	function search_recursive($needle, $haystack) {
		foreach ($haystack as $key => $value) {
			if ($needle === $value || (is_array($value) && suarr::search_recursive($needle, $value) !== false))
				return $key;
		}
		return false;
	}
	
	function vklrsort(&$arr, $valuekey) {
		$valuekey = sustr::preg_filter('A-Za-z0-9', $valuekey);
		uksort($arr, create_function('$a,$b', 'return strlen($b["'.$valuekey.'"]) - strlen($a["'.$valuekey.'"]);'));
	}
	
	function flatten_values($arr, $value_keys) {
		foreach ((array)$value_keys as $key)
			$arr = suarr::_flatten_values($arr, $key);
		return $arr;
	}
	
	function _flatten_values($arr, $value_key = 0) {
		if (!is_array($arr) || !count($arr)) return array();
		$newarr = array();
		foreach ($arr as $key => $array_value) {
			if (is_array($array_value)) {
				if (isset($array_value[$value_key]))
					$newarr[$key] = $array_value[$value_key];
			} elseif (is_object($array_value)) {
				if (isset($array_value->$value_key))
					$newarr[$key] = $array_value->$value_key;
			}
		}
		return $newarr;
	}
}

?>