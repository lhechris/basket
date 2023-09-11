<?php
session_start();

/**
 * Retourne si on est connecté ou pas
 */
function islogged() {
	$ret = false;
	if (array_key_exists('islogged',$_SESSION)) {
		$ret = ($_SESSION['islogged'] == "1");
	}
	return $ret;
}

/**
 * 
 */
function getIslogged() {
	header("Content-Type:text/html");
	header("HTTP/1.1 200");

	if (islogged()) {
		echo "1";
	}else {
		echo "0";
	}
}


/**
 * Verifie les identifiants
 */
function login($email,$passwd) {
	
	if (($email=="coach") && ($passwd=="aslbu11f1")) {
		$_SESSION['islogged']="1";
	} else {
		$_SESSION['islogged']="0";
		
	}
	getIslogged();
}

/**
 * 
 */
function logout() {
	$_SESSION['islogged']="0";
	getIslogged();
}
?>