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
include_once("matchinfos.php");
include_once("donnees.php");

$donnees = new Donnees();
$users = new Users($donnees);
$matchinfo = new MatchInfos($donnees);
$matchs = new Matchs($donnees);
$entrainements=new Entrainements($donnees);
$presences = new Presences($donnees);
$disponibilites = new Disponibilites($donnees);
$selections = new Selections($donnees);


if ($_SERVER["REQUEST_METHOD"]=="GET") { 
	//Gestion du GET
	if (array_key_exists('users',$_GET)) {
		$users->get();

	} else 	if (array_key_exists('matchs',$_GET)) {
		if (islogged()) {
			$matchs->get(); 
		} else {
			responseJson(array());
		}

	} else 	if (array_key_exists('match',$_GET)) {
		if (islogged()) {
			$matchs->get($_GET['match']);
		} else {
			responseJson(array());
		}

	} else 	if (array_key_exists('matchsavecopp',$_GET)) {
		if (islogged()) {
			$matchs->getAvecOppositions();
		} else {
			responseJson(array());
		}

	} else 	if (array_key_exists('matchsavecsel',$_GET)) {
		$matchs->getAvecSelections();

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

	} else 	if (array_key_exists('disponibilites',$_GET)) {		
		$disponibilites->get();

	} else 	if (array_key_exists('selections',$_GET)) {
		if (islogged()) {
			$selections->getOld();
		} else {
			responseJson(array());
		}
	} else 	if (array_key_exists('selections2',$_GET)) {
		if (islogged()) {
			$selections->get();
		} else {
			responseJson(array());
		}	} else 	if (array_key_exists('islogged',$_GET)) {	
			getIslogged();

	} else {
		retourneErreur("Bad Request");
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
		//OPPOSITION
		if (islogged()) {
			return $matchinfo->set($json);
		}
	} else if (array_key_exists("type",$json) && (array_key_exists("tab",$json))&& (islogged())) {
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


?>