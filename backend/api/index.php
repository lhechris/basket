<?php


ini_set("display_errors", 1);

include_once("constantes.php");
include_once("users.php");
include_once("matchs.php");
include_once("entrainements.php");
include_once("auth.php");
include_once("presences.php");
include_once("disponibilites.php");
include_once("selections.php");


if ($_SERVER["REQUEST_METHOD"]=="GET") { 
	//Gestion du GET

 	if (array_key_exists('users',$_GET)) {
		getUsers();

	} else 	if (array_key_exists('matchs',$_GET)) {
		getMatchs();

	} else 	if (array_key_exists('entrainements',$_GET)) {
		getEntrainements();

	} else 	if (array_key_exists('presences',$_GET)) {
		getPresences();

	} else 	if (array_key_exists('disponibilites',$_GET)) {
		getDisponibilites();

	} else 	if (array_key_exists('selections',$_GET)) {
		if (islogged()) {
			getSelections();
		}
	} else 	if (array_key_exists('islogged',$_GET)) {
			getIslogged();
	}


}
else if ($_SERVER["REQUEST_METHOD"]=="POST") 
{
	if (array_key_exists("login",$_POST)) {
		login($_POST['login'],$_POST['passwd']);
		return;
	}

	if (array_key_exists("logout",$_POST)) {
		logout();
		return;
	}

	// Gestion du POST
	$json = json_decode(file_get_contents('php://input'),true);	
	if (!is_array($json)) {	
		retourneErreur("incorrect entry");
		return;

	} else if (array_key_exists("usr",$json) && array_key_exists("match",$json) && array_key_exists("value",$json)) {
		//DISPO
		return setDisponibilite($json);

	} else if (array_key_exists("usr",$json) && array_key_exists("entrainement",$json) && array_key_exists("pres",$json)) {
		//PRESENCE
		return setPresence($json);

	} else if (array_key_exists("usr",$json) && array_key_exists("match",$json) && array_key_exists("selection",$json)) {
		//SELECTION
		if (islogged()) {
			return setSelection($json);
		}

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