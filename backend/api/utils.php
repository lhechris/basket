<?php

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
			loginfo("Erreur prepare");
			return;
		}

		$bindingok=true;

		foreach ($binds as $bind) {
			if (! $stmt->bindValue($bind[0],$bind[1],$bind[2])) {
				$bindingok = false;
			}
		}

        if ($bindingok ) {

            $results = $stmt->execute();
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
				}
				$stmt->reset();        
				return $datas;
			}
        } 
		$stmt->reset(); 
		return false;
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
}

class CommonModelCount extends CommonModel{
	public $count;

	public function from_array(array $data) {
		$this->count =  $this->nullifnotexists($data, "count(*)");
	}	
}


?>