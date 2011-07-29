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
			if(!defined('ROOT_URL'))
			{
				page_header();
			}
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

function geocode($input)
{
	$base_url = "http://maps.google.com/maps/geo?output=xml&key=ABQIAAAAdfp6_8kn9oqveEg9BAz3PRQ_HtiBYhtNgTxmZZfiGMwxv67N2xTiZ";
    $request_url = $base_url . "&q=" . urlencode($input);
    $xml = simplexml_load_file($request_url);
	
    $status = $xml->Response->Status->code;
    if (strcmp($status, "200") == 0)
	{
		// Successful geocode
		$coordinates = $xml->Response->Placemark->Point->coordinates;
		$locality = $xml->Response->Placemark->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
		
		return array($coordinates, $locality);
    }
	else if (strcmp($status, "620") == 0)
	{
		// sent geocodes too fast
		return false;
    }
	else
	{
		// failure to geocode
		return false;
	}
}

function seed_password($seed, $password)
{
    $seed = sha1(md5($seed));
    $password = sha1(md5($password));
    return sha1($seed.$password);
}

function redirect($page, $message)
{
	$page = str_replace('-', '/', $page);
	

	header('Refresh: 2; url=' . ROOT_URL . $page . '/');
	trigger_error($message, E_USER_NOTICE);
}

function detect_mobile_browser()
{
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	
	if(preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
	{
		return true;
	}
	
	return false;
}

function page_header()
{
	global $db, $template, $subdir, $user;
	
	$template->assign_vars(array(
		'ROOT_URL'		=> ROOT_URL,
		'LOGGED_IN'		=> ($user->logged_in != false) ? 1 : 0,
		'USER_ADMIN'	=> ($user->userdata['user_admin']) ? 1 : 0,
		'USER_NAME'		=> (!empty($user->userdata['user_name'])) ? $user->userdata['user_name'] : 'Anonymous',
		'USER_ID'		=> (!empty($user->userdata['user_id'])) ? $user->userdata['user_id'] : 0,
	));
	
	return;
}