<?php
$just_find = true;
require_once('mg_base.class.php');

$module = new MONGORILLA_MODULE;
$db = new MONGORILLA_DB;
$user = $module->load('user');
//print_r($user->get_user(false,'4fb68f052354aebef3b032c8'));
//print_r($user->set_permission(array('BlogPoint' => array('admin'=>1, 'groups'=> array('users','authors','administrators'), 'email'=>'yes@no.com')),'4fb68f052354aebef3b032c8'));
//print_r($user->get_user($user,'4fb68f052354aebef3b032c8'));
print_r($user->get_permission('4fb68f052354aebef3b032c8', 'BlogPoint'));

print_r($db->find(array('where' => array('permissions.BlogPoint.admin' => array('$gte' => 2 )))));

$query = array(
	'type' => 'User',
	'where' => array('username' => 'steve')
	);
//print_r($db->find($query));

//$user_query = array('username' => 'Unique');

//print_r($user->get_user($user_query));

?>