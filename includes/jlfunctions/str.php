<?php
/*
JLFunctions String Class
Copyright (c)2009-2010 John Lamansky
*/

class sustr {
	
	/**
	 * Returns whether or not a given string starts with a given substring.
	 * 
	 * @param string $str The "haystack" string.
	 * @param string $sub The "needle" string.
	 * @return bool Whether or not $str starts with $sub.
	 */
	function startswith( $str, $sub ) {
	   return ( substr( $str, 0, strlen( $sub ) ) === $sub );
	}

	/**
	 * Returns whether or not a given string ends with a given substring.
	 * 
	 * @param string $str The "haystack" string.
	 * @param string $sub The "needle" string.
	 * @return bool Whether or not $str ends with $sub.
	 */
	function endswith( $str, $sub ) {
	   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
	}
	
	function endwith( $str, $end ) {
		if (!sustr::endswith($str, $end))
			return $str.$end;
		else
			return $str;
	}
	
	function has($str, $sub) {
		return (strpos($str, $sub) !== false);
	}
	
	function ihas($str, $sub) {
		$str = strtolower($str);
		$sub = strtolower($sub);
		return (strpos($str, $sub) !== false);
	}

	/**
	 * Truncates a string if it is longer than a given length.
	 * 
	 * @param string $str The string to possibly truncate.
	 * @param int $maxlen The desired maximum length of the string.
	 * @param str $truncate The string that should be added to the end of a truncated string.
	 */
	function truncate( $str, $maxlen, $truncate = '...' ) {
		if ( strlen($str) > $maxlen )
			return substr( $str, 0, $maxlen - strlen($truncate) ) . $truncate;
		
		return $str;
	}
	
	function truncate_at( $str, $end ) {
		if ($endpos = strpos($str, $end))
			return substr($str, 0, $endpos);
		else
			return $str;
	}
	
	/**
	 * Joins strings into a natural-language list.
	 * Can be internationalized with gettext or the su_lang_implode filter.
	 * 
	 * @param array $items The strings (or objects with $var child strings) to join.
	 * @param string|false $var The name of the items' object variables whose values should be imploded into a list.
		If false, the items themselves will be used.
	 * @param bool $ucwords Whether or not to capitalize the first letter of every word in the list.
	 * @return string|array The items in a natural-language list.
	 */
	function nl_implode($items, $var=false, $ucwords=false) {
		
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
	
	/**
	 * If the given string ends with the given suffix, the suffix is removed.
	 * 
	 * @param string $str The string of which the provided suffix should be trimmed if located.
	 * @param string $totrim The suffix that should be trimmed if found.
	 * @return string The possibly-trimmed string.
	 */
	function rtrim_str($str, $totrim) {
		if (strlen($str) > strlen($totrim) && sustr::endswith($str, $totrim))
			return substr($str, -strlen($totrim));
		
		return $str;
	}
	
	function rtrim_substr($str, $totrim) {
		for ($i = strlen($totrim); $i > 0; $i--) {
			$totrimsub = substr($totrim, 0, $i);
			if (sustr::endswith($str, $totrimsub))
				return sustr::rtrim_str($str, $totrimsub);
		}
		
		return $str;
	}
	
	function batch_replace($search, $replace, $subjects) {
		$subjects = array_unique((array)$subjects);
		$results = array();
		foreach ($subjects as $subject) {
			$results[$subject] = str_replace($search, $replace, $subject);
		}
		return $results;
	}
	
	function unique_words($str) {
		$str = explode(' ', $str);
		$str = array_unique($str);
		$str = implode(' ', $str);
		return $str;
	}
	
	function remove_double_words($str) {
		$words = explode(' ', $str);
		foreach ($words as $word) $str = str_replace("$word $word", $word, $str);
		return $str;
	}
	
	function preg_filter($filter, $str) {
		return preg_replace("/[^{$filter}]/", '', $str);
	}
	
	function preg_escape($str, $delim='%') {
		$chars = "\ ^ . $ | ( ) [ ] * + ? { } , ".$delim;
		$chars = explode(' ', $chars);
		foreach ($chars as $char)
			$str = str_replace($char, '\\'.$char, $str);
		return $str;
	}
	
	function htmlsafe_str_replace($search, $replace, $subject, $limit, &$count) {
		$search = sustr::preg_escape($search);
		return sustr::htmlsafe_preg_replace($search, $replace, $subject, $limit, $count);
	}
	
	function htmlsafe_preg_replace($search, $replace, $subject, $limit, &$count) {
		
		//Special thanks to the GPL-licensed "SEO Smart Links" plugin for the following find/replace regex
		//http://www.prelovac.com/vladimir/wordpress-plugins/seo-smart-links
		$reg = '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b($name)\b/imsU';
		
		$search = str_replace('/', '\/', $search);
		$search_regex = str_replace('$name', $search, $reg);
		
		return preg_replace($search_regex, $replace, $subject, $limit, $count);
	}
	
	function upto($str, $sub) {
		$end = strpos($str, $sub);
		if ($end === false)
			return $str;
		else
			return substr($str, 0, $end);
	}
}

?>