<?php

class signup
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
				//Account created, inform user
				
				$template->assign_vars(array(
				    'SUBMIT_SUCCESS' => 1
				));
			}
			else
			{
				//Failed, check for error code
				//handle gracefully
				switch($errorcode)
				{
					case 1:
						trigger_error('ERROR_SIGNUP_USERNAME_SHORT', E_USER_NOTICE);
					break;
					case 2:
						trigger_error('ERROR_SIGNUP_EMAIL_INVALID', E_USER_NOTICE);
					break;
					case 3:
						trigger_error('ERROR_SIGNUP_PASSWORD_SHORT', E_USER_NOTICE);
					break;
					case 4:
						trigger_error('ERROR_SIGNUP_USERNAME_TAKEN', E_USER_NOTICE);
					break;		
					case 5:
						trigger_error('ERROR_SIGNUP_EMAIL_TAKEN', E_USER_NOTICE);
					break;				
					default:
						trigger_error('ERROR_SIGINUP_UNSPECIFIED', E_USER_NOTICE);
					break;
				}
			}
		}
		
		
		$template->set_filenames(array(
			'body'	=> 'signup_body.html',
		));
	}
	
	private function submit()
	{
		global $db, $template, $config;
		
		//Submit
		$username_input = (isset($_POST['username_input_field'])) ? $_POST['username_input_field'] : ''; //Errorno 1
		$email_input = (isset($_POST['email_input_field'])) ? $_POST['email_input_field'] : ''; //Errorno 2
		$password_input = (isset($_POST['password_input_field'])) ? $_POST['password_input_field'] : ''; //Errorno 3
		
		if(strlen($username_input) < 3)
		{
		    return 1;
		}
		
		if(check_email_address($email_input) == false)
		{
		    return 2;
		}
		
		if(strlen($password_input) < 8)
		{
		    return 3;
		}
		
		if($config->user_name_exists($username_input))
		{
		    return 4;
		}
		
		if($config->user_email_exists($email_input))
		{
		    return 5;
		}
		
		$new_password = seed_password($username_input, $password_input);
		
		$sql = "INSERT INTO users 
		    (user_name, user_email, user_password, user_admin, user_approved) 
		    VALUES ('" . $db->sql_escape($username_input) . "', '" . $db->sql_escape($email_input) . "', '" . $db->sql_escape($new_password) . "', 0, 0)";	
		$db->sql_query($sql);
		
		return 0;
	}
}