<?php

/**
* Error and message handler, call with trigger_error if reqd
*/
function msg_handler($errno, $msg_text, $errfile, $errline)
{
	global $db, $template, $msg_title, $msg_long_text;

	include('./lib/lang/error.php');
	
	$msg_title = (isset($lang_error[$msg_text.'_TITLE'])) ? $lang_error[$msg_text.'_TITLE'] : $msg_text.'_TITLE';
	$msg_text = (isset($lang_error[$msg_text.'_DESC'])) ? $lang_error[$msg_text.'_DESC'] : $msg_text.'_DESC';

	switch ($errno)
	{
		case E_NOTICE:
		case E_WARNING:

			// Check the error reporting level and return if the error level does not match
			// If DEBUG is defined the default level is E_ALL
			if (($errno & ((defined('DEBUG')) ? E_ALL : error_reporting())) == 0)
			{
				return;
			}

			if (strpos($errfile, 'cache') === false && strpos($errfile, 'template.') === false)
			{
				// flush the content, else we get a white page if output buffering is on
				if ((int) @ini_get('output_buffering') === 1 || strtolower(@ini_get('output_buffering')) === 'on')
				{
					@ob_flush();
				}
				
				echo '<b>[Debug] PHP Notice</b>: in file <b>' . $errfile . '</b> on line <b>' . $errline . '</b>: <b>' . $msg_text . '</b><br />' . "\n";
				if(defined('DEBUG'))
				{
					echo '<br /><br />BACKTRACE<br />' . get_backtrace() . '<br />' . "\n";
				}
			}

			return;

		break;

		case E_USER_ERROR:

			$msg_title = 'General Error';
			$l_return_index = '<a href="./">Return to index page</a>';
			$l_notify = '<p>Please notify the board administrator or webmaster: <a href="mailto:webmaster@electricnation.nl?subject=Your Sexy Site&amp;body=Fix this shit, niggers.">webmaster@electricnation.nl</a></p>';
						
			$db->sql_close();
			
			// Do not send 200 OK, but service unavailable on errors
			header('HTTP/1.1 503 Service Unavailable');

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">';
			echo '<head>';
			echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
			echo '<title>' . $msg_title . '</title>';
			echo '</head>';
			echo '<body id="errorpage">';
			echo '<div id="wrap">';
			echo '	<div id="page-header">';
			echo '		' . $l_return_index;
			echo '	</div>';
			echo '	<div id="acp">';
			echo '	<div class="panel">';
			echo '		<div id="content">';
			echo '			<h1>' . $msg_title . '</h1>';

			echo '			<div>' . $msg_text . '</div>';

			echo $l_notify;

			echo '		</div>';
			echo '	</div>';
			echo '	</div>';
			echo '</div>';
			echo '</body>';
			echo '</html>';

			// On a fatal error (and E_USER_ERROR *is* fatal) we never want other scripts to continue and force an exit here.
			exit;
		break;
		case E_USER_WARNING:
		case E_USER_NOTICE:
			$template->assign_vars(array(
				'MESSAGE_TITLE'	=> $msg_title,
				'MESSAGE_TEXT'	=> $msg_text,
			));
			
			$template->set_filenames(array(
				'error'	=> 'error_body.html',
			));
			
			$template->display('error');
			$db->sql_close();
			exit;
		break;
	}

	// If we notice an error not handled here we pass this back to PHP by returning false
	// This may not work for all php versions
	return false;
}

function mobile_error_handler($msg_text)
{
	global $db, $template;
	
	//Error handling for within our own site, not used at this time
	$template->assign_vars(array(
		'MESSAGE_TEXT'	=> $msg_text,
		'ERROR_MOBILE'	=> 1,
	));

	$template->set_filenames(array(
		'error'	=> 'iphone_body.html',
	));

	$template->display('error');
	$db->sql_close();
	exit;
}

/**
* Return a nicely formatted backtrace (parts from the php manual by diz at ysagoon dot com)
*/
function get_backtrace()
{
	$output = '<div style="font-family: monospace;">';
	$backtrace = debug_backtrace();
	$path = realpath('./');
	if (substr($path, -1) == DIRECTORY_SEPARATOR)
	{
			$path = substr($path, 0, -1);
	}

	foreach ($backtrace as $number => $trace)
	{
		// We skip the first one, because it only shows this file/function
		if ($number == 0)
		{
			continue;
		}

		// Strip the current directory from path
		if (empty($trace['file']))
		{
			$trace['file'] = '';
		}
		else
		{
			$trace['file'] = str_replace(array($path, '\\'), array('', '/'), $trace['file']);
			$trace['file'] = substr($trace['file'], 1);
		}
		$args = array();

		// If include/require/include_once is not called, do not show arguments - they may contain sensible information
		if (!in_array($trace['function'], array('include', 'require', 'include_once')))
		{
			unset($trace['args']);
		}
		else
		{
			// Path...
			if (!empty($trace['args'][0]))
			{
				$argument = htmlspecialchars($trace['args'][0]);
				$argument = str_replace(array($path, '\\'), array('', '/'), $argument);
				$argument = substr($argument, 1);
				$args[] = "'{$argument}'";
			}
		}

		$trace['class'] = (!isset($trace['class'])) ? '' : $trace['class'];
		$trace['type'] = (!isset($trace['type'])) ? '' : $trace['type'];

		$output .= '<br />';
		$output .= '<b>FILE:</b> ' . htmlspecialchars($trace['file']) . '<br />';
		$output .= '<b>LINE:</b> ' . ((!empty($trace['line'])) ? $trace['line'] : '') . '<br />';

		$output .= '<b>CALL:</b> ' . htmlspecialchars($trace['class'] . $trace['type'] . $trace['function']) . '(' . ((sizeof($args)) ? implode(', ', $args) : '') . ')<br />';
	}
	$output .= '</div>';
	return $output;
}

/* Works out the time since the entry post, takes a an argument in unix time (seconds) */
## alex at nyoc dot net
## Feel free to better for your needs

function timeago($referencedate=0, $timepointer='', $measureby='', $autotext=true){    ## Measureby can be: s, m, h, d, or y
    if($timepointer == '') $timepointer = time();
    $Raw = $timepointer-$referencedate;    ## Raw time difference
    $Clean = abs($Raw);
    $calcNum = array(array('s', 60), array('m', 60*60), array('h', 60*60*60), array('d', 60*60*60*24), array('y', 60*60*60*24*365));    ## Used for calculating
    $calc = array('s' => array(1, 'seconden'), 'm' => array(60, 'minuten'), 'h' => array(60*60, 'uur'), 'd' => array(60*60*24, 'dagen'), 'y' => array(60*60*24*365, 'jaar'));    ## Used for units and determining actual differences per unit (there probably is a more efficient way to do this)
   
    if($measureby == ''){    ## Only use if nothing is referenced in the function parameters
        $usemeasure = 's';    ## Default unit
   
        for($i=0; $i<count($calcNum); $i++){    ## Loop through calcNum until we find a low enough unit
            if($Clean <= $calcNum[$i][1]){        ## Checks to see if the Raw is less than the unit, uses calcNum b/c system is based on seconds being 60
                $usemeasure = $calcNum[$i][0];    ## The if statement okayed the proposed unit, we will use this friendly key to output the time left
                $i = count($calcNum);            ## Skip all other units by maxing out the current loop position
            }       
        }
    }else{
        $usemeasure = $measureby;                ## Used if a unit is provided
    }
   
    $datedifference = floor($Clean/$calc[$usemeasure][0]);    ## Rounded date difference
   
    if($autotext==true && ($timepointer==time())){
        if($Raw < 0){
            $prospect = ' over ';
        }else{
            $prospect = ' geleden';
        }
    }
   
    if($referencedate != 0){        ## Check to make sure a date in the past was supplied
        if($datedifference == 1){    ## Checks for grammar (plural/singular)
			if($prospect == ' over ')
			{
				if($calc[$usemeasure][1] == 'minuten')
				{
					return $datedifference . ' minuut ' . $prospect;
				}
				if($calc[$usemeasure][1] == 'seconde')
				{
					return $datedifference . ' seconde ' . $prospect;
				}
				if($calc[$usemeasure][1] == 'dag')
				{
					return $datedifference . ' dag ' . $prospect;
				}
			
				return $prospect . ' ' . $datedifference . ' ' . $calc[$usemeasure][1] . ' ';
			}
			
			if($calc[$usemeasure][1] == 'minuten')
			{
				return $datedifference . ' minuut ' . $prospect;
			}
			if($calc[$usemeasure][1] == 'seconde')
			{
				return $datedifference . ' seconde ' . $prospect;
			}
			if($calc[$usemeasure][1] == 'dag')
			{
				return $datedifference . ' dag ' . $prospect;
			}
			
			return $datedifference . ' ' . $calc[$usemeasure][1] . ' ' . $prospect;
        }else{
			if($prospect == ' over ')
			{
				return $prospect . ' ' . $datedifference . ' ' . $calc[$usemeasure][1] . ' ';
			}

			return $datedifference . ' ' . $calc[$usemeasure][1] . ' ' . $prospect;
        }
    }else{
        return 'No input time referenced.';
    }
}

function check_email_address($email)
{
	if (!preg_match('/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9_-]+)\.([a-zA-Z0-9\._-]+)/',$email))
	{
		return false;
	}
	else
	{
		return true;
	}
}

function get_licenseplate_sidecode($Licenseplate)
{
	$arrSC = array();
	$scUitz = '';
	$Licenseplate = strtoupper(str_replace('-', '',$Licenseplate));
	$arrSC[0] = '/^[a-zA-Z]{2}[\d]{2}[\d]{2}$/'; // 1 XX-99-99
	$arrSC[1] = '/^[\d]{2}[\d]{2}[a-zA-Z]{2}$/'; // 2 99-99-XX
	$arrSC[2] = '/^[\d]{2}[a-zA-Z]{2}[\d]{2}$/'; // 3 99-XX-99
	$arrSC[3] = '/^[a-zA-Z]{2}[\d]{2}[a-zA-Z]{2}$/'; // 4 XX-99-XX
	$arrSC[4] = '/^[a-zA-Z]{2}[a-zA-Z]{2}[\d]{2}$/'; // 5 XX-XX-99
	$arrSC[5] = '/^[\d]{2}[a-zA-Z]{2}[a-zA-Z]{2}$/'; // 6 99-XX-XX
	$arrSC[6] = '/^[\d]{2}[a-zA-Z]{3}[\d]{1}$/'; // 7 99-XXX-9
	$arrSC[7] = '/^[\d]{1}[a-zA-Z]{3}[\d]{2}$/'; // 8 9-XXX-99
	$arrSC[8] = '/^[a-zA-Z]{2}[\d]{3}[a-zA-Z]{1}$/'; // 9 XX-999-X
	$arrSC[9] = '/^[a-zA-Z]{1}[\d]{3}[a-zA-Z]{2}$/'; // 10 X-999-XX

	//except licenseplates for diplomats
	$scUitz = '/^CD[ABFJNST][0-9]{1,3}$/'; //for example: CDB1 of CDJ45
	for($i=0;$i<count($arrSC);$i++)
	{
		if (preg_match($arrSC[$i],$Licenseplate))
		{
			return $i+1;
		}
	}
	
	if (preg_match($scUitz,$Licenseplate)) {
		return 'CD';
	}
	
	return false;
}

function format_licenseplate($Licenseplate,$Sidecode)
{
	$Licenseplate = strtoupper(str_replace('-', '',$Licenseplate));
	if ($Sidecode <= 6) {
		return substr($Licenseplate,0,2) . '-' . substr($Licenseplate,2,2) . '-' . substr($Licenseplate,4,2);
	}
	
	if ($Sidecode == 7 || $Sidecode == 9)
	{
		return substr($Licenseplate,0,2) . '-' . substr($Licenseplate,2,3) . '-' . substr($Licenseplate,5,1);
	}
	
	if ($Sidecode == 8 || $Sidecode == 10)
	{
		return substr($Licenseplate,0,1) . '-' . substr($Licenseplate,1,3) . '-' . substr($Licenseplate,4,2);
	}
	
	return $Licenseplate;
}

function seed_password($seed, $password)
{
    $seed = sha1(md5($seed));
    $password = sha1(md5($password));
    return sha1($seed.$password);
}

function page_header()
{
	global $db, $template, $subdir, $user;
	
	$root_url = 'http://' . $_SERVER['HTTP_HOST'] . $subdir;
	
	define('ROOT_URL', $root_url);
	
	$template->assign_vars(array(
		'ROOT_URL'	=> ROOT_URL,
	));
	
	return;
}