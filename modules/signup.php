<?php

class signup
{
	function main()
	{	
		global $db, $template, $config;
		
		if(isset($_POST['submit']))
		{
			if($this->submit() == 0)
			{
				//Submitted successfully
			}
			else
			{
				//Failed, check for error code
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
		
		
		return 0;
	}
}