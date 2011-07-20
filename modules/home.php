<?php

class home
{
	function main()
	{	
		global $db, $template, $config, $user;
		//Use this for handling input: http://maps.googleapis.com/maps/api/geocode/json?latlng=LATLNG&sensor=TRUEORFALSE
		
		if(isset($_POST['submit']))
		{
			$errorcode = $this->submit($_POST);
			if($errorcode == 0)
			{
				//Submitted successfully
				redirect('', 'SPOT_SUCCESS');
			}
			else
			{
				//Failed, check for error code
				switch($errorcode)
				{
					case 1:
						trigger_error('ERROR_SPOT_LICENSE_PLATE', E_USER_NOTICE);
					break;
					case 2:
						trigger_error('ERROR_SPOT_GEOCODE', E_USER_NOTICE);
					break;
					case 3:
						trigger_error('ERROR_SPOT_DATE_INVALID', E_USER_NOTICE);
					break;	
					default:
						trigger_error('ERROR_SPOT_UNSPECIFIED', E_USER_NOTICE);
					break;
				}
			}
		}
		
		
		$template->set_filenames(array(
			'body'	=> 'home_body.html',
		));
	}
	
	private function submit($post_data)
	{
		global $db, $template, $config, $user;
		
		
		$license_plate_input = (isset($post_data['license_plate_input'])) ? $post_data['license_plate_input'] : ''; //Errorno 1
		$location_input = (isset($post_data['location_input'])) ? $post_data['location_input'] : ''; //Errorno 2
		$date_input = (isset($post_data['date_input'])) ? explode('-', $post_data['date_input']) : ''; //Errorno 3
			
		if(get_licenseplate_sidecode($license_plate_input) == false)
		{
			return 1;
		}
		
		$location = geocode($location_input);
		
		if($location == false)
		{
			return 2;
		}
		
		$location_coords = $location[0];
		$location_readable = $location[1];
		
		if(sizeof($date_input) != 3)
		{
			return 3;
		}
		
		$timestamp = mktime(0,0,0,$date_input[1],$date_input[0],$date_input[2]);
		
		if($timestamp == false)
		{
			return 3;
		}
		
        $roadster_id = 0;
        
        $sql = "SELECT * 
        	FROM roadster 
        	WHERE roadster_license_plate = '" . $db->sql_escape($license_plate_input) . "'";
		$result = $db->sql_query($sql);       
		$roadster = $db->sql_fetchrow($result);

		$roadster_id = $roadster['roadster_id'];

		if(empty($roadster))
		{
		    //Add new roadster
		    $sql = "INSERT INTO roadster 
		    	(roadster_license_plate) 
		    	VALUES ('" . $db->sql_escape($license_plate_input) . "')";
			$db->sql_query($sql);
		    
			$roadster_id = $db->sql_nextid();
		    //return 1;
		}
		
		//Add spot to the database
		$sql = "INSERT INTO spots 
			(user_id, roadster_id, spot_coordinates, spot_location_readable, spot_date) 
			VALUES ('" . $user->uid . "', '" . $roadster_id . "', '" . $db->sql_escape($location_coords) . "', '" . $db->sql_escape($location_readable) . "', '" . $db->sql_escape($timestamp) . "')";
		$db->sql_query($sql);
		
		return 0;
	}
}