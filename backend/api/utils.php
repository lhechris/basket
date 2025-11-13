<?php

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

	if (!getenv("ACTIVELOG")) {return;}

	if (!$fp = fopen("info.log", 'a')) {
		return;
	}
	fwrite($fp,$msg."\n");
	fclose($fp);
}

?>