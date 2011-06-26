<?php

class home
{
	function main()
	{	
		global $db, $template, $config;
		//Use this for handling input: http://maps.googleapis.com/maps/api/geocode/json?latlng=LATLNG&sensor=TRUEORFALSE
		
		if(isset($_POST['submit']))
		{
			if($this->submit($_POST) == 0)
			{
				//Submitted successfully
			}
			else
			{
				//Failed, check for error code
			}
		}
		
		
		$template->set_filenames(array(
			'body'	=> 'home_body.html',
		));
	}
	
	private function submit($post_data)
	{
		global $db, $template, $config;
		
		
		$license_plate_input = (isset($post_data['license_plate_input'])) ? format_licenseplate($post_data['license_plate_input']) : ''; //Errorno 1
		$location_input = (isset($post_data['location_input'])) ? $post_data['location_input'] : ''; //Errorno 2
		$date_input = (isset($post_data['date_input'])) ? explode('-', $post_data['date_input']) : ''; //Errorno 3
			
		if(get_licenseplate_sidecode($license_plate_input) == false)
		{
			return 1;
		}
		
		if(preg_match('/([0-9.-]+).+?([0-9.-]+)/', $str, $matches) == 0)
		{
			return 2;
		}
		
		$lat=(float)$matches[1];
		$long=(float)$matches[2];
		
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
                
		foreach($config->roadsters as $roadster)
		{
            if($roadster['roadster_license_plate'] == $license_plate_input)
            {
                $roadster_id = $roadster['roadster_id'];
                break;
            }
		}
		
		if($roadster_id == 0)
		{
		    //Add new roadster
		    //return 1;
		}
		
		//Add spot to the database
		
		
		return 0;
	}
}