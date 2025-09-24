<?php 

require_once("utils.php");

class Joueur extends CommonModel{
	public $id, $prenom, $nom,$equipe,$licence,$otm,$charte;

	public function to_array() : array {
		return [
			"id" => $this->$id,
			"prenom" => $this->prenom,
			"nom" => $this->nom ,
			"equipe" => $this->equipe ,
			"licence" => $this->licence ,
			"otm" => $this->otm ,
			"charte" => $this->charte
		];
	}

	public function from_array(array $datas) {
		$this->id = $this->nullifnotexists($datas,"id");
		if ($this->id == null) { $this->id = $this->nullifnotexists($datas,"user");}
		$this->prenom = $this->nullifnotexists($datas,"prenom");
		$this->nom = $this->nullifnotexists($datas,"nom");
		$this->equipe = $this->nullifnotexists($datas,"equipe");
		$this->licence = $this->nullifnotexists($datas,"licence");
		$this->otm = $this->nullifnotexists($datas,"otm");
		$this->charte = $this->nullifnotexists($datas,"charte");
	}
}


class Users {
	private $db;

	public function __construct($donnees) {
		$this->db = $donnees->db;
	}


	public function getArray() {	
		$results = $this->db->query('select id,nom,prenom,equipe,licence,otm,charte from users order by prenom');
		$json = array();

		while ($row = $results->fetchArray()) {
			array_push($json,array( "id" => $row["id"],
									"prenom"=>$row["prenom"],
									"nom"=>$row["nom"],
									"equipe"=>$row["equipe"],
									"licence" =>$row["licence"],
									"otm" => $row["otm"] == 1 ? true : false,
									"charte" =>$row["charte"]== 1 ? true : false));
		}
		return $json;
	}

	public function get() {
		responseJson($this->getArray());
	}


	/**
	 * Modifie/Ajoute/Supprime des users 
	 * En entree un fichier JSON contenant la liste des matchs
	 * Si pas d'ID on ajoute et si param todelete on supprime sinon on modifie
	 */
	public function set($json) {

		foreach($json as $nm) {

			if (is_array($nm) && 
				array_key_exists("prenom",$nm) && 
				array_key_exists("nom",$nm) &&
				array_key_exists("equipe",$nm) && 
				array_key_exists("licence",$nm) &&
				array_key_exists("charte",$nm) &&
				array_key_exists("otm",$nm) ) {

				if (array_key_exists("id",$nm)) {
					if (array_key_exists("todelete",$nm)) {
						$this->supprime($nm["id"]);

					} else {
						$this->update($nm["id"],$nm["prenom"],$nm["nom"],$nm["equipe"],$nm["licence"],$nm["otm"],$nm["charte"]);
					}

				} else {
					/**
					 * Il n'y a pas d'id pour ce match c'est donc un ajout 
					 */
					$this->ajoute($nm["prenom"],$nm["equipe"]);
				}
			}
		}
		$this->get();	
	}

	/**
	 * Execute la requete UPDATE sur la table match
	 */
	protected function update($id,$prenom,$nom,$equipe,$licence,$otm,$charte) {
		
		$stmt = $this->db->prepare(
			'UPDATE users '.
			'SET equipe=:equipe, prenom=:prenom, nom=:nom, licence=:licence, otm=:otm, charte=:charte '.
			'WHERE id=:id');
		
		if (
			($stmt->bindValue(':id', $id, SQLITE3_INTEGER)) &&
			($stmt->bindValue(':equipe', $equipe, SQLITE3_INTEGER)) &&
			($stmt->bindValue(':prenom', $prenom, SQLITE3_TEXT)) &&
			($stmt->bindValue(':nom', $nom, SQLITE3_TEXT)) && 
			($stmt->bindValue(':licence', $licence, SQLITE3_TEXT)) &&
			($stmt->bindValue(':otm', $otm, SQLITE3_INTEGER)) &&
			($stmt->bindValue(':charte', $charte, SQLITE3_INTEGER)) 
		) {
			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {
				loginfo("Erreur");
			}
			$stmt->reset();					

		} else {
			loginfo("Erreur query values");
		}
	}

	/**
	 * Execute la requete INSERT INTO dans la table match
	 * Et ajoute un entree dans les tables disponibilites et selections
	 */
	protected function ajoute($prenom,$equipe) {
		$stmt = $this->db->prepare('INSERT INTO users(prenom,equipe) VALUES(:prenom,:equipe)');
		if (
			($stmt->bindValue(':prenom', $prenom, SQLITE3_TEXT)) &&
			($stmt->bindValue(':equipe', $equipe, SQLITE3_INTEGER)) 
		) {
			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {loginfo("Erreur");}

			//$lastid=$db->lastInsertRowID();				

		} else {
			loginfo("Erreur query values");
		}
	}

	/**
	 * Execute la requete DELETE dans la table match
	 * Supprime aussi les entrees dans disponibilites et selections
	 */
	protected function supprime($id) {
		
		$stmt = $this->db->prepare('DELETE FROM matchs WHERE id=:id');

		if 	($stmt->bindValue(':id', $id, SQLITE3_INTEGER)) 
		{
			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {loginfo("Erreur");}

			//suppression dans disponibilites, presences et selections
			$stmt = $this->db->prepare('DELETE FROM disponibilites WHERE user=:id');
			$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
			
			if ($stmt->execute()===false) {
				loginfo($stmt->getSQL(true));
				loginfo("Erreur");
			}

			$stmt = $db->prepare('DELETE FROM selections WHERE user=:id');
			$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
			
			if ($stmt->execute()===false) {
				loginfo($stmt->getSQL(true));
				loginfo("Erreur");
			}

			$stmt = $db->prepare('DELETE FROM presences WHERE user=:id');
			$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
			
			if ($stmt->execute()===false) {
				loginfo($stmt->getSQL(true));
				loginfo("Erreur");
			}
		} else {
			loginfo("Erreur query values");
		}

	}

}

?>