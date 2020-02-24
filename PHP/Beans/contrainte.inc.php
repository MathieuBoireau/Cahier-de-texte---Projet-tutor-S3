<?php
class Contrainte{

	private $tab;
	private $type;
	private $valeur;

	public function __construct($tab = '', $type = '', $valeur = ''){
		$this->tab    = $tab;
		$this->type   = $type;
		$this->valeur = $valeur;
	}

	public function getTab()   { return $this->tab;    }
	public function getType()  { return $this->type;   }
	public function getValeur(){ return $this->valeur; }
}
?>
