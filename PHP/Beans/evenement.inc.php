<?php

class Evenement {

	private $id_event;
	private $valeur_module;
	private $date_seance;
	private $id_user;
	private $type_seance;
	private $groupe;
	private $type_event;
	private $lib_event;
	private $date_rendu;
	private $duree;

	public function __construct($id_event = '', $valeur_module = '',
		$date_seance = '', $id_user = '', $type_seance = '', $groupe = '',
		$type_event = '', $lib_event = '', $date_rendu = '', $duree = 0.0) {

		$this->id_event      = $id_event;
		$this->valeur_module = $valeur_module;
		$this->date_seance   = $date_seance;
		$this->id_user       = $id_user;
		$this->type_seance   = $type_seance;
		$this->groupe        = $groupe;
		$this->type_event    = $type_event;
		$this->lib_event     = $lib_event;
		$this->date_rendu    = $date_rendu;
		$this->duree         = $duree;
	}

	public function toArray(){
		$retour = array($this->id_event, $this->valeur_module, $this->date_seance, $this->id_user,
			$this->type_seance, $this->groupe, $this->type_event, $this->lib_event, $this->date_rendu, $this->duree);
		return $retour;
	}

	public function estEntreDates($dateDeb, $dateFin){
		$deb = strtotime($dateDeb);
		$fin = strtotime($dateFin);
		$dateRendu = strtotime($this->date_rendu);

		if(($dateRendu >= $deb) && ($dateRendu <= $fin))
			return 1;
		return 0;
	}

	public function getIdEvent()      { return $this->id_event; }
	public function getValeurModule() { return $this->valeur_module; }
	public function getDateSeance($format = 'Y-m-d') {
		return date($format, strtotime($this->date_seance));
	}
	public function getIdUser()       { return $this->id_user; }
	public function getTypeSeance()   { return $this->type_seance; }
	public function getGroupe()       { return $this->groupe; }
	public function getTypeEvent()    { return $this->type_event; }
	public function getLibEvent()     { return $this->lib_event; }
	public function getDateRendu($format = 'Y-m-d') {
		if(strcmp($this->date_rendu, "") == 0)
			return "";
		return date($format, strtotime($this->date_rendu));
	}
	public function getDuree()        { return $this->duree;         }
	public function getPiecesJointes()  {
		$db = DB::getInstance();
		return $db->getPj(array($this->id_event, $this->valeur_module, $this->date_seance, $this->id_user,
			$this->type_seance, $this->groupe));
	}

}
?>
