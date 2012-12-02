<?php

class MONGORILLA_MODULE extends MONGORILLA {
	
	//This is the base module class that all other modules will inherit from. These are not application plugins, but actual modules that add functionality to the base mongorilla class
	// List of modules that are registered with the system
	public $modules;
	
	// configuration options -- don't touch unless you're really modifying mongorilla
	//Directory with modules
	public $module_base_dir = './modules/'; 

	public $module_ext = '.module.php'; // Our Module Extension
	
	private static $instance;
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function load($module) {
		$filename = "./modules/" . strtolower($module) . "/" . strtolower($module) . ".module.php";
		include_once($filename);
		$moduleClass = new $module; // This Will launch our plugin constructor.

		return $moduleClass;
		}
	
	public function action($function){
		if(method_exists($this, $function)) // Check our function exists

		{

			$this->$function(); //Call the function defined above

		}

		else

		{

			die("Function not found!"); // Show error message

		}	
	}
	//Module classes will need to register with Mongorilla. If they do not, they will not be in context and may not be available to the apps that use mongorilla (like MongoPoint)
	public function register($module) {
		if(isset($this->modules[$module->name]) && is_object($this->modules[$module->name])){
			$error = new MONGORILLA_ERRORS;
			$error->error(error_type::Error,error_level::Critical,"This module could not be registered!");
			}
			$this->modules[$module->name] = $module;
	}
	
	public function list_modules(){
		return $modules;
	}
	
	public static function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
?>