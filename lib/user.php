<?php

class user
{
	var $logged_in = false;
	var $userdata = null;
	var $uid;

	function user()
	{
		global $db, $template;
	
		$this->get_session_var();
		if(!empty($this->uid))
		{
			$this->get_userdata();
		}
	}
	
	function log_in($userid)
	{
		$_SESSION['uid'] = $userid;
		$this->get_session_var();
		$this->get_userdata();
		$this->logged_in = true;
	}
	
	private function get_userdata()
	{
		global $db, $template;
		
		if(empty($this->uid))
		{
			$this->userdata = false;
		}
		
		$sql = "SELECT * 
			FROM users
			WHERE user_id = '" . $db->sql_escape($this->uid) . "'";
		$result = $db->sql_query($sql);
		$userdata = $db->sql_fetchrow($result);
		
		$this->userdata = $userdata;
	}
	
	private function get_session_var()
	{
		global $db, $template;
		
		$this->uid = (empty($_SESSION['uid'])) ? false : $_SESSION['uid'];
	}
}