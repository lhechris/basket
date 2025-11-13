<?php 

require_once("utils.php");
require_once("dao/EntrainementsDAO.php");

use dao\EntrainementsDAO;

class Entrainements {
	private $entrainements;

	public function __construct($donnees) {
		$this->entrainements = new EntrainementsDAO($donnees);
	}


	/** Initialise  */
	public function init($first,$second,$nbsemaine) {
		$this->entrainements->deleteAll();

		$dfirst=new Datetime($first);
		$dsecond=new Datetime($second);
		
		for ($i=0;$i<$nbsemaine;$i++) {
			echo(round($i*100/$nbsemaine)."%\r");

			$this->entrainements->create($dfirst->format('Y-m-d'));
			$this->entrainements->create($dsecond->format('Y-m-d'));

			$dfirst->add(new DateInterval('P7D') );
			$dsecond->add(new DateInterval('P7D') );

		}
	}

	public function getArray() {

		return $this->entrainements->getAll();

	}

	public function get() {
		
		responseJson($this->getArray());
	}
}


?>