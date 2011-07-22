<?php

class roadster_list
{	
	function main()
	{
		global $db, $template, $config, $user;
		
		$sql = "SELECT * 
			FROM roadster";
		$result = $db->sql_query($sql);
		$roadsterlist = $db->sql_fetchrowset($result);
		
		if(empty($roadsterlist))
		{
			$template->assign_block_vars('roadster_listing_empty', array());
		
		}
		
		foreach ($roadsterlist as $roadster)
		{
			$template->assign_block_vars('roadster_listing', array(
				"ROADSTER_LISTING_ID"			=> $roadster['roadster_id'],
				"ROADSTER_LISTING_PLATE"		=> $roadster['roadster_license_plate'],
				"ROADSTER_LISTING_COLOR"		=> (isset($roadster['roadster_color'])) ? $roadster['roadster_color'] : 'Onbekend',
				"ROADSTER_LISTING_TYPE"			=> (isset($roadster['roadster_type'])) ? $roadster['roadster_type'] : 'Onbekend',
				"ROADSTER_LISTING_YEAR"			=> (isset($roadster['roadster_year'])) ? $roadster['roadster_year'] : 'Onbekend',
				"ROADSTER_LISTING_ON_ROAD"		=> $roadster['roadster_on_road'],
			));
		}
		
		
		$template->set_filenames(array(
			'body'	=> 'roadster_list_body.html',
		));
	}
	
	function delete()
	{
		global $db, $template, $config, $user;
		
		if(($user->userdata['user_admin'] == 0) || ($user->logged_in == 0))
		{
			redirect('', 'NOT_AUTHORIZED');
		}
		
		$roadsterid = (isset($_GET['input'])) ? $_GET['input'] : 0;
		$redirect = (isset($_GET['redirect'])) ? $_GET['redirect'] : 'user_list/';
		
		if($roadsterid == 0)
		{
			trigger_error("Input invalid");
		}
		
		$sql = "SELECT * 
			FROM roadster 
			WHERE roadster_id = '" . $db->sql_escape($roadsterid) . "'";
		$result = $db->sql_query($sql);
		$view_roadster = $db->sql_fetchrow($result);
		
		if(empty($view_roadster))
		{
			trigger_error('user does not exist');
		}
		
		//Delete spots
		$sql = "DELETE FROM spots 
			WHERE roadster_id = '" . $db->sql_escape($roadsterid) . "'";
		$db->sql_query($sql);
		
		//Delete roadster
		$sql = "DELETE FROM roadster 
			WHERE roadster_id = '" . $db->sql_escape($roadsterid) . "'";
		$db->sql_query($sql);
		
		
		redirect($redirect, 'Roadster deleted');
	}
}