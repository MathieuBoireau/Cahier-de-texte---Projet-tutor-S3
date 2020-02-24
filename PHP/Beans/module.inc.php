<?php

include_once "DB.inc.php";

class Module {

	private static $db;
	private $valeur_module;
	private $lib_module;
	private $couleur;
	private $droit;

	public function __construct($valeur_module = '', $lib_module = '', $couleur = '', $droit = '') {
		$this->valeur_module = $valeur_module;
		$this->lib_module    = $lib_module;
		$this->couleur       = $couleur;
		$this->droit         = $droit;

		if (is_null(Module::$db)) {
			Module::$db = DB::getInstance();
		}
	}

	public function getValeurModule() { return $this->valeur_module; }
	public function getLibModule()    { return $this->lib_module; }
	public function getCouleur()      { return $this->couleur; }
	public function getDroit()        { return $this->droit; }
	public function possedeAffectation($enseignant) {
		$utilisateursAffectes = Module::$db->getAffectations($this->valeur_module);
		foreach($utilisateursAffectes as $utilisateur) {
			if($utilisateur->getId() == $enseignant->getId())
				return true;
		}
		return false;
	}
}
?>
