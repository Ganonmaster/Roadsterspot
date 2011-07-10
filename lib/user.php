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
			$this->logged_in = true;
		}
	}
	
	function update_env()
	{
		global $db, $template;
		
		$template->assign_vars(array(
			'LOGGED_IN'		=> ($this->logged_in != false) ? 1 : 0,
			'USER_ADMIN'	=> ($this->userdata['user_admin']) ? 1 : 0,
			'USER_NAME'		=> (!empty($this->userdata['user_name'])) ? $this->userdata['user_name'] : 'Anonymous',
			'USER_ID'		=> (!empty($this->userdata['user_id'])) ? $this->userdata['user_id'] : 0,
		));
	}
	
	function log_in($userid)
	{
		$_SESSION['uid'] = $userid;
		$this->get_session_var();
		$this->get_userdata();
		$this->logged_in = true;
		$this->update_env();
	}
	
	function log_out()
	{
		unset($_SESSION['uid']);
		$this->logged_in = false;
		$userdata = null;
		$logged_in = false;
		$this->update_env();
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