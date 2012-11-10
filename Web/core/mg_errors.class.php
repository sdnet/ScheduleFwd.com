<?php

// This class handles ALL errors/notifications/warnings from the mongorilla framework.

class MONGORILLA_ERRORS extends MONGORILLA {

	public $id;
	public $type;
	public $level;
	public $description;
	/**
	 * errors constructor
	 *
	 * @param 
	 */
	public function __construct() {

		if (! class_exists('MONGORILLA_DB')) {
			trigger_error("MONGORILLA_DB Class Required",E_USER_WARNING);
			die();
		}
		//parent::__construct();
		
		$this->mb = new MONGORILLA_DB;
	}

	public function get_error_types(){
		$type_enum = array(error_type::Error,error_type::Notice,error_type::Warning);
		return $type_enum;
	}
	
	public function get_error_levels(){
		$level_enum = array(error_level::Critical,error_level::Moderate,error_level::Low);
		return $level_enum;	
	}
	
	public function get_error_type_id(string $type){
		$type_id;
		$type_enum = new ReflectionClass('error_type');
		if(in_array($type, $type_enum))
		{
			foreach($type_enum as $key => $value)
			{
				if($key == $type)
				{
					$type_id = $value;
				}	
			}
			
			return $type_id;
		}
		else{
			return $this->create_error(error_type::Warning,error_level::Low, "Could not find type " . $type); 	
		}	
	}
		
	
	public function create_error($type, $level, $description) {
		//First we attempt to insert the error into the db
		//Assemble it into an object before passing
		$error_object = array(
					'obj'	=> array(
						'name'	=> 'error',
						'type'	=> '' . $type . '',
						'level'	=> '' . $level . '',
						'description' => '' . $description . ''
					)
				);
		
		//to do check error handling
		//MONGOBASE	
		
		$message = $this->mb->upsert($error_object);
		//can be used like $message->error->type etc
		return $message;
	}
	
	public function get_error($id) {
		$search = array(
			'_id' => new MongoId($id)
			);
		$result = $mb->find($search);
		
		return $result;
	}
	
	public function get_error_bytype(int $type){
		$search = array(
			'type' => '' . $type . ''
			);
		
		$result = $mb->find($search);
		
		return $result;
	}
}

class error_type
	{
		const Error = 1;	
		const Warning = 2;
		const Notice = 3;
	}

class error_level
	{
		const Critical = 1;	
		const Moderate = 2;
		const Low = 3;
	}
?>