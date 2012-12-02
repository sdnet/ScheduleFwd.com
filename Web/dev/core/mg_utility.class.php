<?php

// This class provides utility functions for the mongorilla framework.

class MONGORILLA_UTILITY extends MONGORILLA {
	
	/*
	  Summary:  Converts any string to UTF-8
	  Input:    String to be converted
	  Output:   Converted string 
	*/
	public function string_to_utf8($inStr) {
		$retString = utf8_encode($inStr);
		return $retString;
	}

	/*
	  Summary:  Takes in a string and validates it as an email address
	  Input:    String to be validated
	  Output:   Boolean, true or false 
	*/	
	public function is_valid_email($inEmail) {
		$retValid = false;
		if (filter_var($inEmail, FILTER_VALIDATE_EMAIL)) {
			$retValid = true;
		}
		return $retValid;	
	}
	
	function get_subclasses($parent) {
		$result = array();
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, $parent))
				$result[] = $class;
		}
	}
	
}

?>