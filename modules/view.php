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

		$sql = "SELECT s.*, u.*, r.*
			FROM spots s, users u, roadster r 
			WHERE s.user_id = '" . $view_user['user_id'] . "'
				AND u.user_id = '" . $view_user['user_id'] . "'
				AND r.roadster_id = s.roadster_id";
		$result = $db->sql_query($sql);
		$spots = $db->sql_fetchrowset($result);
		
		$template->assign_vars(array(
			"SHOW_SPOTS"	=> (empty($spots)) ? 1 : 0,
		));

		if(!empty($spots))
		{
			foreach($spots as $spot)
			{
				$template->assign_block_vars('spot_list', array(
					"SPOT_ID"					=> $spot['spot_id'],
					"SPOT_ROADSTER_PLATE"		=> $spot['roadster_license_plate'],
					"SPOT_ROADSTER_ID"			=> $spot['roadster_id'],
					"SPOT_USER_ID"				=> $spot['user_id'],
					"SPOT_USER"					=> $spot['user_name'],
					"SPOT_LOCATION"				=> $spot['spot_location_readable'],
					"SPOT_TIME"					=> date('l jS \of F Y h:i:s A', $spot['spot_date']),
				));
			}
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
			"VIEW_ROADSTER_PLATE"	=> $view_roadster['roadster_license_plate'],
			"VIEW_ROADSTER_ID"		=> $view_roadster['roadster_id'],
		));
		
		$sql = "SELECT s.*, u.*, r.*
			FROM spots s, users u, roadster r 
			WHERE s.roadster_id = '" . $view_roadster['roadster_id'] . "'
				AND u.user_id = s.user_id
				AND r.roadster_id = s.roadster_id";
		$result = $db->sql_query($sql);
		$spots = $db->sql_fetchrowset($result);
		
		$template->assign_vars(array(
			"SHOW_SPOTS"	=> (empty($spots)) ? 1 : 0,
		));

		if(!empty($spots))
		{
			foreach($spots as $spot)
			{
				$template->assign_block_vars('spot_list', array(
					"SPOT_ID"					=> $spot['spot_id'],
					"SPOT_ROADSTER_PLATE"		=> $spot['roadster_license_plate'],
					"SPOT_ROADSTER_ID"			=> $spot['roadster_id'],
					"SPOT_USER_ID"				=> $spot['user_id'],
					"SPOT_USER"					=> $spot['user_name'],
					"SPOT_LOCATION"				=> $spot['spot_location_readable'],
					"SPOT_TIME"					=> date('l jS \of F Y ', $spot['spot_date']),
				));
			}
		}
		
		$template->set_filenames(array(
			'body'	=> 'view_roadster.html',
		));
	}
	
	function delete()
	{
		global $db, $user, $template;
		
		$input = (isset($_GET['input'])) ? $_GET['input'] :  '';
		$redirect = (isset($_GET['redirect'])) ? $_GET['redirect'] :  '';
		
		if(empty($input))
		{
			redirect($redirect, 'INPUT_EMPTY');
		}
		
		$inputarray = explode('-', $input);
		
		if($inputarray[0] == 'spot')
		{
			$this->delete_spot($inputarray[1], $redirect);
		}
		else
		{
			redirect($redirect, 'NO_SPOT');
		}
		
		redirect($redirect, 'DELETED_THING');
	}
	
	private function delete_spot($id, $redirect)
	{
		global $db, $user, $template;

		$sql = "SELECT * 
			FROM spots 
			WHERE spot_id = '" . $db->sql_escape($id) . "'";
		$result = $db->sql_query($sql);
		$spot = $db->sql_fetchrow($result);
		
		if(empty($spot))
		{
			redirect($redirect, 'NO_SUCH_SPOT');
		}
		
		$authorized = false;
		
		if(($spot['spot_id'] == $user->uid) || ($user->userdata['user_admin'] == 1))
		{
			$authorized = true;
		}
		
		if($authorized == false)
		{
			redirect($redirect, 'NOT_AUTHORIZED');
		}
		
		$sql = "DELETE FROM spots 
			WHERE spot_id = '" . $db->sql_escape($id) . "'";
		$result = $db->sql_query($sql);
		
		redirect($redirect, 'DELETED_SPOT');
	}
	
}