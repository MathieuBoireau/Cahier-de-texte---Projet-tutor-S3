<?php

class Groupe {

	private $groupe;
	private $groupepere;
	private static $db;


	public function __construct($groupe = "", $groupepere = "") {
		$this->groupe = $groupe;
		$this->groupepere = $groupepere;

		if (is_null(Groupe::$db)) {
			Groupe::$db = DB::getInstance();
		}
	}


	public function getGroupe()	    { return $this->groupe;	}
	public function getGroupePere()	{ return $this->groupepere;	}

	public function getGroupeEtFils(){
		$groupes = Groupe::$db->getGroupes();
		$groupePereFils = array($this);

		for($i=0; $i<sizeof($groupePereFils); $i++){
			for($j=0; $j<sizeof($groupes); $j++)
				if($groupes[$j]->getGroupePere() == $groupePereFils[$i]->getGroupe())
					array_push($groupePereFils,$groupes[$j]);
		}
		return $groupePereFils;
	}
}
 ?>
