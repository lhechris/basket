<?php

ini_set("display_errors", 1);

include_once("env.php");
loadEnv(".env");

include_once("utils.php");
include_once("users.php");
include_once("matchs.php");
include_once("entrainements.php");
include_once("auth.php");
include_once("presences.php");
include_once("disponibilites.php");
include_once("selections.php");
include_once("oppositions.php");
include_once("donnees.php");

$donnees = new Donnees();
$users = new Users($donnees);
$oppositions = new Oppositions($donnees);
$matchs = new Matchs($donnees,$oppositions);
$entrainements=new Entrainements($donnees);
$presences = new Presences($donnees,$users,$entrainements);
$disponibilites = new Disponibilites($donnees,$users,$matchs);
$selections = new Selections($donnees,$users,$matchs,$disponibilites);


if ($_SERVER["REQUEST_METHOD"]=="GET") { 
	//Gestion du GET

 	if (array_key_exists('users',$_GET)) {
		$users->get();

	} else 	if (array_key_exists('matchs',$_GET)) {
		$matchs->get();

	} else 	if (array_key_exists('match',$_GET)) {
		$matchs->get($_GET['match']);

	} else 	if (array_key_exists('entrainements',$_GET)) {
		if (islogged()) {
			$entrainements->get();
		} else {
			responseJson(array());
		}

	} else 	if (array_key_exists('presences',$_GET)) {
		if (islogged()) {
			$presences->get();
		} else {
			responseJson(array());
		}
	
	} else 	if (array_key_exists('oppositions',$_GET)) {
		loginfo("arf");
		if (islogged()) {
			$oppositions->get($_GET['oppositions']);
		} else {
			responseJson(array());
		}

	} else 	if (array_key_exists('disponibilites',$_GET)) {
		$disponibilites->get();

	} else 	if (array_key_exists('selections',$_GET)) {
		if (islogged()) {
			$selections->get();
		} else {
			responseJson(array());
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

	} else if (array_key_exists("usr",$json) && array_key_exists("jour",$json) && array_key_exists("value",$json)) {
		//DISPO
		return $disponibilites->set($json);

	} else if (array_key_exists("usr",$json) && array_key_exists("entrainement",$json) && array_key_exists("pres",$json)) {
		//PRESENCE
		if (islogged()) {
			return $presences->set($json);
		}

	} else if (array_key_exists("usr",$json) && array_key_exists("match",$json) && array_key_exists("selection",$json)) {
		//SELECTION
		if (islogged()) {
			return $selections->set($json);
		}

	} else if (array_key_exists("usr",$json) && array_key_exists("match",$json) && array_key_exists("opposition",$json)) {
		//SELECTION
		loginfo("chuilà");
		if (islogged()) {
			return $oppositions->set($json);
		}
	
	} else if (array_key_exists("type",$json) && (array_key_exists("tab",$json))) {
		if ($json["type"]=="matchs") {
			//MATCHS
			$matchs->set($json["tab"]);
		
		} else if ($json["type"]=="match") {
			//MATCH
			$matchs->set($json["tab"]);
		
		} else if ($json["type"]=="users") {
			//USERS
			$users->set($json["tab"]);

		} else {
			retourneErreur("Invalid Request");	
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