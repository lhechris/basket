<?php


ini_set("display_errors", 1);

include_once("users.php");
include_once("matchs.php");
include_once("auth.php");
include_once("presences.php");


if ($_SERVER["REQUEST_METHOD"]=="GET") { 
	//Gestion du GET

 	if (array_key_exists('users',$_GET)) {
		getUsers();

	} else 	if (array_key_exists('matchs',$_GET)) {
		getMatchs();

	} else 	if (array_key_exists('presences',$_GET)) {
		getPresences();
	}


}
else if ($_SERVER["REQUEST_METHOD"]=="POST") 
{
	if (!islogged()) {
		retourneNotAuth();
	}

	// Gestion du POST
	$json = json_decode(file_get_contents('php://input'),true);	
	if (!is_array($json)) {	
		retourneErreur("incorrect entry");
		return;
	}

	if (array_key_exists("login",$json)) {
		//LOGIN
		login($json);
		return;
	
	} else if (array_key_exists("logout",$json)) {
		//LOGOUT
		logout();
		return;

	} else if (array_key_exists("usr",$json) && array_key_exists("match",$json) && array_key_exists("value",$json)) {
		//PRESENCE
		return setPresence($json);

	} else {
		retourneErreur("Invalid Request");	
	}
}
else 
{
	retourneErreur("Invalid Request");
}

/**
 * 
 */
function retourneErreur($content) {
	header("Content-Type:text/html");
	header("HTTP/1.1 400");
	echo ($content);
}

/**
 * 
 */
function retourneNotAuth() {
	header("Content-Type:text/html");
	header("HTTP/1.1 401");
	echo ("Désolé");
}


?>