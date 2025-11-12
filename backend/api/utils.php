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



class CommonCtrl {
    protected $db;

    public function __construct($donnees) {
        $this->db = $donnees->db;
    }

	protected function query(string $sql,array $binds,string $classname=null) {
		
		$stmt = $this->db->prepare($sql);
		if ($stmt===false) {
			loginfo("Erreur prepare : ".$this->db->lastErrorMsg());
			loginfo($sql);
			return;
		}

		$bindingok=true;

		foreach ($binds as $bind) {
			if (! $stmt->bindValue($bind[0],$bind[1],$bind[2])) {
				loginfo("Erreur binding : ".$this->db->lastErrorMsg());
				loginfo($sql);
				$bindingok = false;
			}
		}

        if ($bindingok ) {

            $results = $stmt->execute();
			//loginfo($stmt->getSQL(true));

            if ($results===false) {
				loginfo($stmt->getSQL(true));
                loginfo("Erreur");				
            } else {				
				$datas = array();
				if ($classname !== null) {
					while ($row = $results->fetchArray()) {
						$obj = new $classname();
						$obj->from_array($row);
						array_push($datas,$obj);
					}
				} else {
					$datas = true;
				}

				$stmt->reset(); 
				return $datas;
			}
        } 
		$stmt->reset(); 
		return false;
	}

	// execute un select count(*)
	public function querycount(string $table,string $where, array $binds) {
		$query = 'SELECT count(*) FROM '.$table.' WHERE '.$where;
		$result = $this->query($query,$binds,'CommonModelCount');

		if ($result === false) { return -1;}

		return $result[0]->count;
	}


	//retourne s'il y a au moins un enregistrement
	public function queryexists(string $table,string $where, array $binds) {

		$result = $this->querycount($table,$where,$binds);

		if ($result === false) { return -1;}

		return $result >= 1;
	}	


	// parcours recursif
	public function to_array($array) {
		$ret = array();		
		foreach($array as $item) {
			array_push($ret,$item->to_array());
		}
		return $ret;
	}
}


class CommonModel {
	protected function nullifnotexists($array, $key) {
		if (array_key_exists($key,$array)) {
			return $array[$key];
		} else {
			return null;
		}
	}

	protected function toarrayrecursif($array) {
		$results = null;
		if (is_array($array)) {
			$results=array();
			foreach ($array as $item) {
				array_push($results,$item->to_array());
			}
		}
		return $results;

	}


}

class CommonModelCount extends CommonModel{
	public $count;

	public function from_array(array $data) {
		$this->count =  $this->nullifnotexists($data, "count(*)");
	}	
}


?>