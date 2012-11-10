<?php

class user extends MONGORILLA_MODULE {
	
	//This handles user management for all apps based on mongorilla.
	public $module = "user";
	
	//Construct, check to see if its registered and if not, then auto register the module
	public function __construct() {
		parent::__construct();
	}
	
	//Module classes will need to register with Mongorilla. If they do not, they will not be in context and may not be available to the apps that use mongorilla (like MongoPoint)
	public function list_users($user = false){	
		$db = new MONGORILLA_DB;
		if($this->is_set($user)){
		}
		else{
			$user = array('username' => array('$exists' => true));
		}
		
		$query = array(
			'where' => $user,
			'type' => 'User'
			);
		$results = $db->find($query);
		foreach($results as $obj){
			$list[] = $obj;	
		}
		return $list;
	}
	
	public function add_user($user,$id = false){
		$user_array = array(
			'obj'   => $user,
			'type' => 'User',
			'partial' => true,
			'id'	=> $id
			);
		
		$db = new MONGORILLA_DB;
		try{
			$result = $db->upsert($user_array);
			foreach($result as $obj){
				return $obj;	
			}	
		}
		catch(Exception $e){
			return $this->__('Error: ').get_class($e).' : '.$e->getMessage();
		}
	}	
	
	public function get_user($user = false,$id = false){
		
		if(!$id)
		{
			$user_array = array(
				'where' => $user,
				'type'  => 'User'
				);
		}
		elseif($id){
			$user_array = array(
				'id'	=> $id
				);
		}
		$db = new MONGORILLA_DB;
		try{
			$results = $db->find($user_array);
			if($this->is_set($results))
			{
				$combined_array = array();
				$i = 0;
				foreach($results as $result){
					$combined_array[$i] = $result;
					$i++;
				}
			return $combined_array;	
			}
			else
			{
				return array('Error' => 'User Not Found');	
			}
		}
		catch(Exception $e){
			return $this->__('Error: ').get_class($e).' : '.$e->getMessage();
		}
	}
	
	public function set_permission($user, $id, $app){
		
		$combo = array($app => $user);
		$permissions = array('permissions' => $combo);
		$user_array = array(
			'obj'   => $permissions,
			'partial' => true,
			'id'	=> $id
			);
		
		$db = new MONGORILLA_DB;
		try{
			$results = $db->upsert($user_array);
			if(isset($results)){
				$combined_array = array();
				$i = 0;
				foreach($results as $result){
					$combined_array[$i] = $result;
					$i++;
				}
			}
			
		    return $combined_array;	
		}
		catch(Exception $e){
			return $this->__('Error: ').get_class($e).' : '.$e->getMessage();
		}
	}	
	
	public function get_permission($id, $app = false){
		
		if($this->is_set($id))
		{
			$user_array = array(
				'where'   => $id,
				'keys' => 'permissions',
				'type' => 'User'
				);
		}
		else{
			$user_array = array(
				'id'	=> $id,
				'type' => 'User',
				'keys' => 'permissions'
				);
		}
		
		if($app)
		{
			$user_array['keys'] = "permissions." . $app . "";
		}
		
		$db = new MONGORILLA_DB;
		try{
			$results = $db->find($user_array);
			if(isset($results)){
				$combined_array = array();
				$i = 0;
				foreach($results as $result){
					$combined_array[$i] = $result;
					$i++;
				}
			}
			
			return $combined_array;	
		}
		catch(Exception $e){
			return $this->__('Error: ').get_class($e).' : '.$e->getMessage();
		}
	}		
}

class user_object extends user {
	
	private $username;
	private $id;
	private $session;
	private $last_action;
	private $permissions;
	
	public function __construct($user_id,$session_id) {
		$this->id = $user_id;
		$this->session = $session_id;
	}
		
}
?>