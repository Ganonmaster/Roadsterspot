<?php

class view
{	
	
	function main()
	{
		trigger_error('fap');
	}
	
	function user()
	{
		global $db, $user, $template;
		
		$userid = (isset($_GET['input'])) ? $_GET['input'] : 0;
		
		if($userid == 0)
		{
			trigger_error("Input invalid");
		}
		
		$sql = "SELECT * 
			FROM users 
			WHERE user_id = '" . $db->sql_escape($userid) . "'";
		$result = $db->sql_query($sql);
		$view_user = $db->sql_fetchrow($result);
		
		if(empty($view_user))
		{
			trigger_error('user does not exist');
		}
		
		$template->assign_vars(array(
			"VIEW_PROFILE_USERNAME"	=> htmlspecialchars($view_user['user_name']),
			"VIEW_PROFILE_USER_ID"	=> $view_user['user_id'],
		));
		
		if($user->userdata['user_admin'] == 1)
		{
			$template->assign_vars(array(
				"VIEW_PROFILE_USER_APPROVED"	=> $view_user['user_approved'],
			));
		}
		
		if(($userid == $user->uid) || ($user->userdata['user_admin'] == 1))
		{
			$template->assign_vars(array(
				"EDIT_USER_PROFILE"		=> 1,
			));
		}
		
		$template->set_filenames(array(
			'body'	=> 'view_user.html',
		));
	}
	
	function roadster()
	{
		global $db, $user, $template;
		
		$roadster = (isset($_GET['input'])) ? $_GET['input'] : 0;
		
		if($roadster == 0)
		{
			trigger_error("Input invalid");
		}
		
		$sql = "SELECT * 
			FROM roadster 
			WHERE roadster_id = '" . $db->sql_escape($roadster) . "'";
		$result = $db->sql_query($sql);
		$view_roadster = $db->sql_fetchrow($result);
		
		if(empty($view_roadster))
		{
			trigger_error('roadster does not exist');
		}
		
		$template->assign_vars(array(
			"VIEW_ROADSTER_USERNAME"	=> htmlspecialchars($view_roadster['roadster_license_plate']),
			"VIEW_ROADSTER_USER_ID"		=> $view_roadster['roadster_id'],
		));
		
		$template->set_filenames(array(
			'body'	=> 'view_user.html',
		));
		
		
	}
}