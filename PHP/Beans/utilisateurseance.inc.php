<?php

include_once "utilisateur.inc.php";
include_once "DB.inc.php";

class Utilisateurseance {

	private static $db;
	private $valeur_module;
	private $date_seance;
	private $id_user;
	private $type_seance;
    private $groupe;
    private $utilisateur;
    private $semaphore;

	public function __construct($valeur_module = null, $date_seance = '', $id_user = '', $type_seance = '', $groupe = '', $utilisateur='', $semaphore ='') {
		$this->valeur_module = $valeur_module;
		$this->date_seance   = $date_seance;
		$this->id_user       = $id_user;
		$this->type_seance   = $type_seance;
        $this->groupe        = $groupe;
        $this->utilisateur   = $utilisateur;
        $this->semaphore     = $semaphore;

        
		if (is_null(Utilisateurseance::$db)) {
			Utilisateurseance::$db = DB::getInstance();
		}
    }
    
    public function getValeurModule() { return $this->valeur_module; }
	public function getDateSeance($format = 'Y-m-d') {
		return date($format, strtotime($this->date_seance));
	}
	public function getIdUser()       { return $this->id_user; }
	public function getTypeSeance()   { return $this->type_seance; }
	public function getGroupe()       { return $this->groupe; }
    public function getUtilisateur()  { return $this->utilisateur; }
    public function getSemaphore()    { return $this->semaphore; }

}
?>
