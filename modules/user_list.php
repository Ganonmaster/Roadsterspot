<?php

class user_list
{
	function user_list()
	{	
		global $db, $template, $config, $user;
		if(($user->userdata['user_admin'] == 0) || ($user->logged_in == 0))
		{
			redirect('', 'NOT_AUTHORIZED');
		}
	}
	
	function main()
	{	
		global $db, $template, $config, $user;
		
		$sql = "SELECT * 
			FROM users";
		$result = $db->sql_query($sql);
		$userlists = $db->sql_fetchrowset($result);
		
		foreach ($userlists as $userdata)
		{
			$template->assign_block_vars('user_listing', array(
				"USER_LISTING_ID"			=> $userdata['user_id'],
				"USER_LISTING_NAME"			=> $userdata['user_name'],
				"USER_LISTING_EMAIL"		=> $userdata['user_email'],
				"USER_LISTING_APPROVED"		=> $userdata['user_approved'],
				"USER_LISTING_ADMIN"		=> $userdata['user_admin'],
			));
		}
		
		
		$template->set_filenames(array(
			'body'	=> 'user_list_body.html',
		));
	}
	
	function admin()
	{
		global $db, $template, $config, $user;
		
		$userid = (isset($_GET['input'])) ? $_GET['input'] : 0;
		$redirect = (isset($_GET['redirect'])) ? $_GET['redirect'] : 'user_list/';
		
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
		
		if($userid == $user->uid)
		{
			trigger_error('can\'t demote yourself');
		}
		
		$permission_setting = ($view_user['user_admin'] == 0) ? 1 : 0;
		
		$sql = "UPDATE users 
			SET user_admin = '" . $permission_setting . "' 
			WHERE user_id = '" . $db->sql_escape($userid) . "'";
		$db->sql_query($sql);
		
		redirect($redirect, 'User permissions changed');
	}
	
	function approve()
	{
		global $db, $template, $config, $user;
		
		$userid = (isset($_GET['input'])) ? $_GET['input'] : 0;
		$redirect = (isset($_GET['redirect'])) ? $_GET['redirect'] : 'user_list/';
		
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
		
		if($userid == $user->uid)
		{
			trigger_error('can\'t demote yourself');
		}
		
		$approval_setting = ($view_user['user_approved'] == 0) ? 1 : 0;
		
		$sql = "UPDATE users 
			SET user_approved = '" . $approval_setting . "' 
			WHERE user_id = '" . $db->sql_escape($userid) . "'";
		$db->sql_query($sql);
		
		redirect($redirect, 'User approved changed');
	}
}