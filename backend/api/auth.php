<?php
session_start();

/**
 * Retourne si on est connecté ou pas
 */
function islogged() {
	//$ret = false;
	$ret = true;
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
function login($json) {
	if (array_key_exists('ident',$json) && array_key_exists('password',$json)) {
		$ident=$json['ident'];
		$password=$json['password'];
		if (($ident=="admin") && ($password=="labobinettec'estbien")) {
			$_SESSION['islogged']="1";
		} else {
			$_SESSION['islogged']="0";
		}
	}
	getIslogged();
}

/**
 * Retourne si on est connecté ou pas
 */
function logout() {
	$_SESSION['islogged']="0";
	getIslogged();
}
?>