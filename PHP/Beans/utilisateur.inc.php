<?php

include_once "DB.inc.php";

class Utilisateur {

	private $id_user;
	private $nom;
	private $prenom;
	private $mdp;
	private $role_utilisateur;
	private $cree_le;
	private $maj_le;
	private $mdpgenere;
	private static $db;

	public function __construct($id_user = '', $nom = '', $prenom = '', $mdp = '', $role_utilisateur = '', $cree_le = '', $maj_le = '', $mdpgenere = '') {
		$this->id_user = $id_user;
		$this->nom     = $nom;
		$this->prenom  = $prenom;
		$this->mdp    = $mdp;
		$this->role_utilisateur = $role_utilisateur;
		$this->cree_le   = $cree_le;
		$this->maj_le    = $maj_le;
		$this->mdpgenere = $mdpgenere;

		if (is_null(Utilisateur::$db)) {
			Utilisateur::$db = DB::getInstance();
		}
	}

	public function getId()       { return $this->id_user; }
	public function getNom()      { return $this->nom; }
	public function getPrenom()   { return $this->prenom; }
	public function getHash()     { return $this->mdp; }
	public function getRole()     { return $this->role_utilisateur; }
	public function getCreation($format = 'Y-m-d') {
		return date($format, strtotime($this->cree_le));
	}
	public function getAnneeCreation() {
		return substr($this->cree_le, 0,4);
	}
	public function getMaj($format = 'Y-m-d') {
		return date($format, strtotime($this->maj_le));
	}

	public function getMdpGenere(){ return $this->mdpgenere; }

	public function getModules(){
		if(Utilisateur::$db == null)
			Utilisateur::$db = DB::getInstance();
		return Utilisateur::$db->getModulesByUser($this->id_user);
	}

	public function getRoleComplet(){
		switch ($this->role_utilisateur) {
			case 'A':
				$role = 'Administrateur';
				break;
			case 'AE':
				$role = 'Enseignant-Administrateur';
				break;
			case 'E':
				$role = 'Enseignant';
				break;
			case 'T':
				$role = 'Etudiant tuteur';
				break;
		}
		return $role;
	}

	// Fonction comparant le rôle de l'utilisateur au droit en paramètre
	// Considère ici que A et AE sont exactement les même rôles en terme de droit
	public function compareDroitRole($droit) {
		if(strcmp($droit,$this->role_utilisateur) == 0)
			return true;
		// if($droit === "AE" && $this->role_utilisateur  === "E" || $droit === "E" && $this->role_utilisateur === "AE")
		// 	return true;
		if(strpos($this->role_utilisateur, $droit) || strpos($droit, $this->role_utilisateur))
			return true;
		return false;
	}

	public function aAccesGroupe($groupe){
		if (is_null(Utilisateur::$db)) {
			Utilisateur::$db = DB::getInstance();
		}
		if(strcmp($this->role_utilisateur, 'T') == 0){
			$groupes = Utilisateur::$db->getGroupesAffectes($this->id_user);
			foreach($groupes as $g){
				if(strcmp($groupe, $g->getGroupe()) == 0)
					return true;
			}
			return false;
		}
		return true;
	}

	public function getGroupes(){
		if (is_null(Utilisateur::$db)) {
			Utilisateur::$db = DB::getInstance();
		}

		if($this->role_utilisateur != "T")
			return Utilisateur::$db->getGroupes();

		$groupesAffectesSansFils = Utilisateur::$db->getGroupesAffectes($this->id_user);
		$groupesAffectes = array();
		foreach($groupesAffectesSansFils as $groupe){
			/*foreach($groupe->getGroupeEtFils() as $groupePereOuFils){
				array_push($groupesAffectes, Utilisateur::$db->getGroupe($groupePereOuFils));
			}*/
			foreach($groupe->getGroupeEtFils() as $groupePereOuFils){
				array_push($groupesAffectes, $groupePereOuFils);
			}
		}
		return $groupesAffectes;
	}

	public function verifUtilAModule($valeur_module){
		if($this->role_utilisateur != "T")
			return true;
		else
			return Utilisateur::$db->verifUtilAModule($this->id_user, $valeur_module);
	}
}
?>
