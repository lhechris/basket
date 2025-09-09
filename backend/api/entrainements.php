<?php 

require_once("constantes.php");

/** Initialise  */
function initEntrainements($first,$second,$nbsemaine) {
	$db = new SQLite3(DBLOCATION);
	$db->query('DELETE FROM entrainements');

	$dfirst=new Datetime($first);
	$dsecond=new Datetime($second);
	
	for ($i=0;$i<$nbsemaine;$i++) {
		echo(round($i*100/$nbsemaine)."%\r");
		$stmt = $db->prepare("INSERT INTO entrainements(jour) VALUES(:jour)");
		$stmt->bindValue(':jour', $dfirst->format('Y-m-d'), SQLITE3_TEXT);
		$stmt->execute();

		$stmt = $db->prepare("INSERT INTO entrainements(jour) VALUES(:jour)");
		$stmt->bindValue(':jour', $dsecond->format('Y-m-d'), SQLITE3_TEXT);
		$stmt->execute();

		$dfirst->add(new DateInterval('P7D') );
		$dsecond->add(new DateInterval('P7D') );

	}


}

function upgradeEntrainementsFromfiles() {
	$fullpath = REPERTOIRE_DATA."entrainements.json";
	if (!file_exists($fullpath)) { return;}

    //Recupere le fichier json
	$json = json_decode(file_get_contents($fullpath),true);	
	if (!is_array($json)) {	return; }

	$db = new SQLite3(DBLOCATION);

	foreach($json as $e) {
		$stmt = $db->prepare("INSERT INTO entrainements(jour) VALUES(:jour)");
		if ($stmt === false) {return;}
		$stmt->bindValue(':jour', $e["date"], SQLITE3_TEXT);
		$stmt->execute();
	}
}
	

function getEntrainementsArray() {

	$db = new SQLite3(DBLOCATION);
	$results = $db->query('select id,jour from entrainements');
	$json = array();

	while ($row = $results->fetchArray()) {
		array_push($json,array( "id" => $row["id"],
								"jour"=>$row["jour"]));
	}

	return $json;

}

function getEntrainements() {
	
	$json=getEntrainementsArray();
	$ret=[];
	responseJson($json);
}



?>