<?php

class logout
{
	function main()
	{
		global $user;
		
		$user->log_out();
		
		redirect('', 'LOGOUT_SUCCESS');
	}
}