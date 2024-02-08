<?php

define("REPERTOIRE_DATA","../../data/");
define("ACTIVELOG",true);
define("DBLOCATION","../../data/basketu11.db");

function responseError($msg) {
    header("Content-Type:text/html");
	header("HTTP/1.1 400");
	echo $msg;
}

function responseJson($json) {
    header("Content-Type:application/json");
	header("HTTP/1.1 200");
	echo json_encode($json);
}

function responseText($msg) {
    header("Content-Type:text/html");
	header("HTTP/1.1 200");
	echo $msg;

}

function loginfo($msg) {

	if (!ACTIVELOG) {return;}

	if (!$fp = fopen("info.log", 'a')) {
		return;
	}
	fwrite($fp,$msg."\n");
	fclose($fp);
}


?>