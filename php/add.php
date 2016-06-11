<?php
if(!isset($_POST['domain'], $_POST['list'], $_POST['token']))
    die("Missing POST variables");

// Check CORS
if($_SERVER['HTTP_ORIGIN'] == "http://pi.hole" || $_SERVER['HTTP_ORIGIN'] == "http://${_SERVER['SERVER_ADDR']}")
    header("Access-Control-Allow-Origin: ${_SERVER['HTTP_ORIGIN']}");
else if($_SERVER['HTTP_HOST'] == $_SERVER['SERVER_ADDR'] || $_SERVER['HTTP_HOST'] == "pi.hole")
    header("Access-Control-Allow-Origin: ${_SERVER['HTTP_HOST']}");
else
    die("Failed CORS");

session_start();

// Check CSRF token
if(!hash_equals($_SESSION['token'], $_POST['token']))
    die("Wrong token");

switch($_POST['list']) {
    case "white":        
        echo getErrorCode(shell_exec(("sudo pihole -w ${_POST['domain']}")));
        break;
    case "black":
        echo getErrorCode(shell_exec(("sudo pihole -b ${_POST['domain']}")));
        break;
}

function getErrorCode($input)
{
	$array = preg_split('/\n+/', trim($input));
	switch(true){
		case stristr($array[1], 'already exists'):
			return "1";
			break;
		case stristr($array[1], 'not a valid'):
			return "1";
			break;
		default:
		 return "0";		
	}
}
