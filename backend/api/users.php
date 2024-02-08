<?php 

require_once("constantes.php");

/** Initialise  */
function initUsers() {
	$db = new SQLite3(DBLOCATION);
	$db->query('DELETE FROM users');
}

/** Rempli la BD depuis un fichier json */
function upgradeUsersFromfiles() {
	$fullpath = REPERTOIRE_DATA."users.json";
	if (!file_exists($fullpath)) { return;}

    //Recupere le fichier json
	$json = json_decode(file_get_contents($fullpath),true);	
	if (!is_array($json)) {	return; }

	$db = new SQLite3(DBLOCATION);

	foreach($json as $u) {
		$stmt = $db->prepare("INSERT INTO users(nom) VALUES(:nom)");
		if ($stmt === false) {return;}
		$stmt->bindValue(':nom', $u["name"], SQLITE3_TEXT);
		$stmt->execute();
	}
}

function getUsersArray() {
	$db = new SQLite3(DBLOCATION);
	$results = $db->query('select * from users');
	$json = array();

	while ($row = $results->fetchArray()) {
		array_push($json,array( "id" => $row["id"],
								"name"=>$row["nom"]));
	}

	return $json;


}

function getUsers() {

	responseJson(getUsersArray());

}

?>