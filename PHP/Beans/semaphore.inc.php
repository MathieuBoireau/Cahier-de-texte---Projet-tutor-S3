<?php

include_once "utilisateur.inc.php";
include_once "DB.inc.php";

class Semaphore {

	private $etat;
	private $couleurnonvu;
	private $couleurvu;

	public function __construct($etat = '', $couleurnonvu = '', $couleurvu = '') {
		$this->etat         = $etat;
		$this->couleurnonvu = $couleurnonvu;
		$this->couleurvu    = $couleurvu;
    }
    
	public function getEtat()      {return $this->etat; }
	public function getCoulNonVu() { return $this->couleurnonvu; }
    public function getCoulVu()    { return $this->couleurvu; }

}
?>
