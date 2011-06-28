<?php

class config
{
	var $users = array();
	var $roadsters = array();

	function get_config()
	{
		global $db, $template;

		$this->get_users();
		$this->get_roadsters();
	}
	
	function get_users()
	{
		global $db, $template;
		
		$sql = "SELECT user_name, user_id 
			FROM users";
		$result = $db->sql_query($sql);
		while($users = $db->sql_fetchrow($result))
		{
			$this->users[$users['user_id']] = $users;
		}
		$db->sql_freeresult($result);
	}

	function get_roadsters()
	{
		global $db, $template;
		
		$sql = "SELECT *
			FROM roadster";
		$result = $db->sql_query($sql);
		while($roadster = $db->sql_fetchrow($result))
		{
			$this->roadsters[$roadster['roadster_id']] = $roadster;
		}
		$db->sql_freeresult($result);
	}
	
	function user_name_exists($username)
	{
	    global $db;
	
	    $sql = "SELECT user_id 
	        FROM users 
	        WHERE user_name = '" . $db->sql_escape($username). "'";
		$result = $db->sql_query($sql);
		$user = $db->sql_fetchrow($result);
		if(!empty($user))
		{
		    return true;
		}
		
		return false;
	}
	
	function user_email_exists($email)
	{
	    global $db;
	
	    $sql = "SELECT user_id 
	        FROM users 
	        WHERE user_email = '" . $db->sql_escape($email). "'";
		$result = $db->sql_query($sql);
		$user = $db->sql_fetchrow($result);
		if(!empty($user))
		{
		    return true;
		}
		
		
		return false;
	}
}