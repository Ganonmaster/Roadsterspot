<?php

class edit_roadster
{	

	function edit_roadster()
	{	
		global $db, $template, $config, $user;
		if(($user->userdata['user_admin'] == 0) || ($user->logged_in == 0))
		{
			redirect('', 'NOT_AUTHORIZED');
		}
	}
	
	function main()
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
			"VIEW_ROADSTER_COLOR"	=> $view_roadster['roadster_color'],
			"VIEW_ROADSTER_TYPE"	=> $view_roadster['roadster_type'],
			"VIEW_ROADSTER_YEAR"	=> $view_roadster['roadster_year'],
		));

		$template->set_filenames(array(
			'body'	=> 'edit_roadster.html',
		));
	}
		
	function submit()
	{
		global $db, $user, $template;
		
		$roadster = (isset($_GET['input'])) ? $_GET['input'] : 0;
		
		$color_field = (isset($_POST['color_field'])) ? $_POST['color_field'] : '';
		$type_field = (isset($_POST['type_field'])) ? $_POST['type_field'] : '';
		$year_field = (isset($_POST['year_field'])) ? $_POST['year_field'] : '';
		
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

		$sql = "UPDATE roadster 
			SET roadster_color = '" . $db->sql_escape($color_field) . "', roadster_type = '" . $db->sql_escape($type_field) . "', roadster_year = '" . $db->sql_escape($year_field) . "'
			WHERE roadster_id = '" . $db->sql_escape($roadster) . "'";
		$db->sql_query($sql);
		
		redirect('view/roadster/' . $roadster , 'ROADSTER_UPDATED');
	}
}