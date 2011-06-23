<?php

class config
{
	var $users = array();

	function get_config()
	{
		global $db, $template;

		$this->get_users();
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
}