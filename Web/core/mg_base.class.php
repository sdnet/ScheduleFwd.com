<?php

require('config.php');



class MONGORILLA {

	/* This is the very generic, base class for all of Mongorilla's functions */
	
	//This is a public options data member that holds all that options for connecting to the database in the mg_db class.
	public $options = null;


	// classes that inherit from this should use parent::__construct(); 
	function __construct(){
		$this->options();
	}
	
	// Using the magic of php, we make sure everything is defined before we attempt to load
	function __autoload($classname) {
		$filename = "./". $classname .".php";
		include_once($filename);
	}
	

	public function settings($options,$defaults = array()){
		if(! $options === false && is_array($options)){
			return array_merge($defaults,$options);
		}
		return $defaults;
	}
	
	public function get_options(){
		return $this->options;
	}

	public function get_option($key){
		return $this->options[$key];
	}

	public function set_option($key,$val){
		$this->options[$key] = $val;
		return $val;
	}

	public function __($key){
		if (function_exists('__')) return __($key);
		return $key;
	}

	public function register_configuration_setting($key, $definition = false, $constant = false, $default = null){
		/* requires that values are defined somewhere - probably in the config module */
		if($default===null) $default = $constant;
		if($definition !== false && $constant !== false){
			if(defined($definition)) $val = $constant;
			else $val = $default;
		}else{
			$val = $default;
		}
		$this -> set_option($key,$val);
	}

	public function options(){
		if($this->options !== null) return $this->options;
		$this->options = array();
		if(!defined('DEMO_CONFIG')) define('DEMO_CONFIG', 'default_value');
		$this->register_configuration_setting('demo_key', 'DEMO_CONFIG', DEMO_CONFIG);
		return $this->options;
	}

	public function is_set($array,$count=0,$field=false){
		$is_set = false;
		if($field){
			if(isset($array[$field])){
				$array[] = $array[$field];
			}else{
				$array = false;
			}
		}
		if(isset($array)){
			if(is_array($array)){
				if(count($array)>$count){
					$is_set = true;
				}
			}
		} return $is_set;
	}

	public function mg_dump($arg){
		//Debug dump for objects
		$debug = '<br /><br />Debug Output:<br /><pre>';
		$debug.= print_r($arg,true);
		$debug .= '</pre><br /><br />';
		return $debug;
	}
	
	function getSubclasses($parentClassName)
	{
    $classes = array();
    foreach (get_declared_classes() as $className)
    {
    	if (is_subclass_of($className, $parentClassName))
    		$classes[] = $className;
    }

    return $classes;
	}

}