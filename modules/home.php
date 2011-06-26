<?php

class home
{
	function main()
	{	
		global $db, $template;
		
		$template->set_filenames(array(
			'body'	=> 'home_body.html',
		));
	}
}