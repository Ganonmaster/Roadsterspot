<?php
session_start();

//We need these
require('./lib/config.php');
require('./lib/functions.php');
require('./lib/functions_default.php');
require('./lib/user.php');
require('./lib/template/template.php');
require('./lib/dbal/' . $dbms . '.php');

//Reroute all errors to our sexy error handler
set_error_handler('msg_handler');
define('MOBILE_BROWSER', detect_mobile_browser());

$db = new $sql_db();
$db->sql_connect($dbserv, $dbuser, $dbpass, $dbname, $dbport);
$template = new template();
$template->set_template();
$config = new config();
$config->get_config();
$user = new user();
page_header();

$template->assign_vars(array(
	"IS_MOBILE"	=> MOBILE_BROWSER,
));

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
			trigger_error('The specified method wasn not found.');
		}
	}
	else
	{
		trigger_error('The specified class was not found.');
	}
}
else
{
	trigger_error('The specified module was not found.');
}

header('HTTP/1.1 200 OK');
header('Content-type: text/html; charset=utf-8');
$template->display('body');
$db->sql_close();
exit;