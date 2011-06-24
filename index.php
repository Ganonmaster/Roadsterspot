<?php
session_start();

//We need these
require('./lib/config.php');
require('./lib/functions.php');
require('./lib/functions_default.php');
require('./lib/template/template.php');
require('./lib/dbal/' . $dbms . '.php');

//Reroute all errors to our sexy error handler
set_error_handler('msg_handler');
ini_set('session.cookie_lifetime', 1440);
ini_set('session.gc_maxlifetime', 1440);


$db = new $sql_db();
$db->sql_connect($dbserv, $dbuser, $dbpass, $dbname, $dbport);
$template = new template();
$template->set_template();
$config = new config();
$config->get_config();
page_header();

if(!empty($_SESSION['user_id']))
{
	$sql = "SELECT user_name, user_email, user_password, user_id
		FROM users
		WHERE user_id = '" . $_SESSION['user_id'] . "'";
	$result = $db->sql_query($sql);
	$user_data = $db->sql_fetchrow($result);
}
else
{
	$user_data = array();
}


$module = (isset($_REQUEST['module'])) ? $_REQUEST['module'] : 'home';
$method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : 'main';

if(file_exists('./modules/' . $module . '.php'))
{
	include('./modules/' . $module . '.php');
	if(class_exists($module))
	{
		$module_exec = new $module;
		if(method_exists($module_exec, $method))
		{
			$module_exec->$method();
		}
		else
		{
			trigger_error('Invalid method.');
		}
	}
	else
	{
		trigger_error('Invalid module.');
	}
}
else
{
	trigger_error('Invalid module1.');
}

$template->assign_vars(array(
	'LOGIN'		=> (isset($_SESSION['user_id'])) ? 1 : 0,
	'USER_ID'	=> (!empty($user_data)) ? $user_data['user_id'] : 0,
));

header('Content-type: text/html; charset=utf-8');
$template->display('body');
$db->sql_close();
exit;