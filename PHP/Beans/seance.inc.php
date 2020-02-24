<?php

include_once "utilisateur.inc.php";
include_once "DB.inc.php";

class Seance {

	private static $db;
	private $valeur_module;
	private $date_seance;
	private $id_user;
	private $type_seance;
	private $groupe;

	public function __construct($valeur_module = null, $date_seance = '', $id_user = '', $type_seance = '', $groupe = '') {
		$this->valeur_module = $valeur_module;
		$this->date_seance   = $date_seance;
		$this->id_user       = $id_user;
		$this->type_seance   = $type_seance;
		$this->groupe        = $groupe;

		if (is_null(Seance::$db)) {
			Seance::$db = DB::getInstance();
		}
	}

	public function getValeurModule() { return $this->valeur_module; }
	public function getDateSeance($format = 'Y-m-d') {
		return date($format, strtotime($this->date_seance));
	}
	public function getIdUser()       { return $this->id_user; }
	public function getTypeSeance()   { return $this->type_seance; }
	public function getGroupe()       { return $this->groupe; }

	public function getSemaine(){
		return date('W',strtotime($this->date_seance));
	}

	public function getNomModule() {
		return Seance::$db->getNomModule($this->valeur_module)[0];
	}

	public function getUser() {
		$user = Seance::$db->getUtilisateur($this->id_user);
		return $user[0]->getPrenom().' '.$user[0]->getNom();
	}

	public function getEvents(){
		//echo Seance::$db->getEvents($this)[0]->getLibEvent();
		return Seance::$db->getEvents($this);
	}

	public function coulModule(){
		return Seance::$db->coulModule($this->valeur_module);
	}

	public function estParentDe($event) {
		return $this->valeur_module == $event->getValeurModule() &&
		       $this->date_seance == $event->getDateSeance() &&
			   $this->id_user == $event->getIdUser() &&
			   $this->type_seance == $event->getTypeSeance() &&
			   $this->groupe == $event->getGroupe();
	}

	public function estEntreDates($dateDeb, $dateFin){
		$deb = strtotime($dateDeb);
		$fin = strtotime($dateFin);
		$dateSeance = strtotime($this->date_seance);

		return (($dateSeance >= $deb) && ($dateSeance <= $fin));
	}
}
?>
