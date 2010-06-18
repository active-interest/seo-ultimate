<?php
/*
JLFunctions HTML Class
Copyright (c)2010 John Lamansky
*/

class suhtml {
	
	/**
	 * Returns <option> tags.
	 */
	function option_tags($options, $current = true) {
		$html = '';
		foreach ($options as $value => $label) {
			$html .= "<option value='$value'";
			if ($value == $current) $html .= " selected='selected'";
			$html .= ">$label</option>";
		}
		return $html;
	}
}

?>