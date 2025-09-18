<?php 

require_once("utils.php");

class Entrainements {
	private $db;

	public function __construct($donnees) {
		$this->db = $donnees->db;
	}


	/** Initialise  */
	public function init($first,$second,$nbsemaine) {
		$this->db->query('DELETE FROM entrainements');

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

	public function getArray() {

		$results = $this->db->query('select id,jour from entrainements order by jour');
		$json = array();

		while ($row = $results->fetchArray()) {
			array_push($json,array( "id" => $row["id"],
									"jour"=>$row["jour"]));
		}

		return $json;

	}

	public function get() {
		
		$json=$this->getArray();
		responseJson($json);
	}
}


?>