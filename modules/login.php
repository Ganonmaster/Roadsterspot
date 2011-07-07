<?php

class login
{
	function main()
	{	
		global $db, $template, $config;
		
		if(isset($_POST['submit']))
		{
		    $errorcode = $this->submit();
			if($errorcode == 0)
			{
				//Submitted successfully
				//Log in
				trigger_error('LOGIN_SUCCESS', E_USER_NOTICE);
			}
			else
			{
				//Failed, check for error code
				//handle gracefully
				switch($errorcode)
				{
					case 1:
						trigger_error('ERROR_LOGIN_USERNAME_SHORT', E_USER_NOTICE);
					break;
					case 2:
						trigger_error('ERROR_LOGIN_PASSWORD_SHORT', E_USER_NOTICE);
					break;
					case 3:
						trigger_error('ERROR_LOGIN_USERNAME_NOT_FOUND', E_USER_NOTICE);
					break;
					case 4:
						trigger_error('ERROR_LOGIN_PASSWORD_WRONG', E_USER_NOTICE);
					break;		
					case 5:
						trigger_error('ERROR_LOGIN_NOT_APPROVED', E_USER_NOTICE);
					break;				
					default:
						trigger_error('ERROR_LOGIN_UNSPECIFIED', E_USER_NOTICE);
					break;
				}
			}
		}
		
		exit;
	}
	/*
		Return values
		0 - OK
		1 - Error username too short
		2 - Error password too short
		3 - Username does not exist
		4 - Password wrong
		5 - User not approved
	*/
	private function submit()
	{
		global $db, $template, $config, $user;
		
		//Submit
		$username_input = (isset($_POST['username_login_input'])) ? $_POST['username_login_input'] : '';
		$password_input = (isset($_POST['password_login_input'])) ? $_POST['password_login_input'] : ''; 
		
		if(strlen($username_input) < 3)
		{
		    return 1;
		}
		
		if(strlen($password_input) < 8)
		{
		    return 2;
		}
		
		$sql = "SELECT * 
			FROM users 
			WHERE user_name = '" . $db->sql_escape($username_input) . "'";
		$result = $db->sql_query($sql);
		$user_info = $db->sql_fetchrow($result);
		
		if(empty($user_info))
		{
		    return 3;
		}
		
		$seeded_password = seed_password($username_input, $password_input);
		
		if($user_info['user_password'] != $seeded_password)
		{
			return 4;
		}
		
		if($user_info['user_approved'] == 0)
		{
			return 5;
		}
		
		$user->login($user_info['user_id']);
		
		return 0;
	}
}