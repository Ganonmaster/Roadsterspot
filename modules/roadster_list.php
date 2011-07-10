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
}