<?php

include_once "Beans/seance.inc.php";
include_once "Beans/utilisateur.inc.php";
include_once "Beans/evenement.inc.php";
include_once "Beans/groupe.inc.php";
include_once 'Beans/module.inc.php';
include_once 'Beans/tuteurgroupe.inc.php';
include_once 'Beans/contrainte.inc.php';
include_once 'Beans/utilisateurseance.inc.php';
include_once 'Beans/piecesjointes.inc.php';

class DB {
	private static $instance = null;
	private $connect=null;

	private function __construct() {
		// Identifiants de connexion à la base de données
		$host = 'woody';
		$port = '5432';
		$db   = 'bp177152';
		$user = 'bp177152';
		$pwd  = 'noisette';

		$connStr = "pgsql:host=$host port=$port dbname=$db";
		try {
			$this->connect = new PDO($connStr, $user, $pwd);
			$this->connect->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
			$this->connect->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			// Ignorer les erreurs sur la connexion.
		}
		return null;
	}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			try {
				self::$instance = new DB();
			}
			catch (PDOException $e) {echo $e;}
		}

		$obj = self::$instance;

		if (($obj->connect) == null) {
			self::$instance=null;
		}

		return self::$instance;
	}

	public function close() {
		$this->connect = null;
	}


	/**
	 * Exécute une requête et crée l'objet dans PHP.
	 * @param string $requete La requête SQL.
	 * @param array $tparam Le tableau comportant les paramètres de la requête
	 * (autant de paramètres que de points d'interrogation dans la requête).
	 * @param string $nomClasse Le nom de classe dans PHP. (doit être inclue dans ce script).
	 * @return array les tuples de la requête sous forme d'objet.
	 */
	private function execQuery($requete,$tparam,$nomClasse) {

		$stmt = $this->connect->prepare($requete);
		$stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $nomClasse);

		if ($tparam != null)
			$stmt->execute($tparam);
		else
			$stmt->execute();

		$tab = array();
		$tuple = $stmt->fetch();
		if ($tuple) {
			$i = 0;
			while ($tuple != false) {
				$tab[$i]=$tuple;
				$tuple = $stmt->fetch();
				$i++;
			}
		}
		return $tab;
	}

	/**
	 * Exécute une requête et retourne le nombre de tuples modifiés.
	 * @param string $requete La requête SQL.
	 * @param array $tparam Le tableau comportant les paramètres de la requête
	 * (autant de paramètres que de points d'interrogation dans la requête).
	 * @return int le nombre de tuples modifiés.
	 */
	private function execMaj($ordreSQL,$tparam) {
		$stmt = $this->connect->prepare($ordreSQL);
		$res = $stmt->execute($tparam);
		return $stmt->rowCount();
	}

	/**
	 * Exécute une requête et retourne le premier tuple.
	 * @param string $requete La requête SQL.
	 * @param array $tparam Le tableau comportant les paramètres de la requête
	 * (autant de paramètres que de points d'interrogation dans la requête).
	 * @return mixed le premier tuple de la requête.
	 */
	private function execSimple($requete, $tparam) {
		$stmt = $this->connect->prepare($requete);
		$stmt->execute($tparam);

		return $stmt->fetch();
	}

	/**
	 * Exécute une requête.
	 * @param string $requete La requête SQL.
	 * @param array $tparam Le tableau comportant les paramètres de la requête
	 * (autant de paramètres que de points d'interrogation dans la requête).
	 * @return array les tuples renvoyés par la requête.
	 */
	private function multiExecSimple($requete, $tparam) {
		$stmt = $this->connect->prepare($requete);
		if ($tparam != null)
			$stmt->execute($tparam);
		else
			$stmt->execute();

		$tab = array();
		$tuple = $stmt->fetch();
		if ($tuple) {
			$i = 0;
			while ($tuple != false) {
				$tab[$i]=$tuple;
				$tuple = $stmt->fetch();
				$i++;
			}
		}
		return $tab;
	}

	// Fonctions qui nous sont propres

	/**
	 * Teste si les identifiants de connexion sont valides.
	 * @param string L'identifiant de l'utilisateur.
	 * @param string Le mot de passe de l'utilisateur.
	 * @return Utilisateur[] l'utilisateur qui s'est connecté.
	 */
	public function connexion($id, $mdp) {
		// obtenir l'utilisateur
		$requete = "select * from utilisateurs where id_user = ?";
		$tparam = array($id);
		$util = $this->execQuery($requete, $tparam, 'Utilisateur');

		// Test si le mot de passe entré correspond au hachage
		// Si oui, retourner l'utilisateur
		// sinon, retourner un tableau vide.
		if ($util && password_verify($mdp, $util[0]->getHash())) {
			return $util;
		} else {
			return array(new Utilisateur());
		}
	}

	// Utilisateur

	/**
	 * Obtient tous les utilisateurs triés par rôle.
	 * @return Utilisateur[] Un tableau qui contient tous les utilisateurs.
	 */
	public function getUtilisateurs() {
		$requete = "select * from utilisateurs order by(
				case role_utilisateur
					when 'A' then 1
					when 'AE' then 2
					when 'E' then 3
					when 'T' then 4
					END
				), id_user ASC;";
		return $this->execQuery($requete, null, 'Utilisateur');
	}

	/**
	 * Obtient un utilisateur.
	 * @param string $id_user L'identifiant de l'utilisateur.
	 * @return Utilisateur
	 */
	public function getUtilisateur($id_user) {
		$requete = "select * from utilisateurs where id_user = ?";
		$tparam = array($id_user);
		return $this->execQuery($requete, $tparam, 'Utilisateur');
	}

	/**
	 * Supprime un utilisateur de la base de données.
	 * @param string $id L'identifiant de l'utilisateur.
	 * @return int 1 si l'utilisateur a été supprimé, 0 sinon.
	 */
	public function supprimerUtilisateur($id) {
		$requete = "delete from utilisateurs where id_user = ?";
		$tparam = array($id);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Modifie les informations d'un utilisateur.
	 * @param string $utilisateurId L'identifiant de l'utilisateur
	 * pour lequel modifier les informations.
	 * @param string $nom Le nouveau nom de l'utilisateur.
	 * @param string $prenom Le nouveau prénom de l'utilisateur.
	 * @param string $role_utilisateur Le nouveau rôle de l'utilisateur
	 * (qui doit correspondre aux contraintes de la base de données).
	 * @return int 1 si l'utilisateur a été modifié, 0 sinon.
	 */
	public function modifierUtilisateur($utilisateurId ,$nom, $prenom, $role_utilisateur) {
		$requete = "update utilisateurs set nom = ?, prenom = ?, role_utilisateur = ? where id_user = ?";
		$tparam = array($nom, $prenom, $role_utilisateur, $utilisateurId);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Encrypte le mot de passe utilisateur et le met à jour.
	 * @param string $utilisateurId L'identifiant de l'utilisateur.
	 * @param string $mdp Le nouveau mot de passe.
	 * @param string $mdpGenere Le mot de passe généré.
	 * @return int 1 si le mot de passe a été mis à jour, 0 sinon.
	 */
	public function setMdpUtilisateur($utilisateurId, $mdp, $mdpGenere){
		$requete = "update utilisateurs set mdp = ?, mdpGenere = ? where id_user = ?";
		// On hache le "vrai" mot de passe, on garde le mdp généré en clair
		// pour que l'admin puisse le donner à l'utilisateur d'une quelconque façon.
		$mdp = password_hash($mdp, PASSWORD_DEFAULT);
		$tparam = array($mdp, $mdpGenere, $utilisateurId);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Ajoute un utilisateur dans la base de données avec un mot de passe encrypté.
	 * @param string $utilisateurId L'identifiant de l'utilisateur.
	 * @param string $nom Le nom de l'utilisateur.
	 * @param string $prenom Le prénom de l'utilisateur.
	 * @param string $mdp Le mot de passe généré aléatoirement à la création.
	 * @param string $role_utilisateur Le rôle de cet utilisateur.
	 * @return int 1 si l'utilisateur a été ajouté, 0 sinon.
	 */
	public function ajouterUtilisateur($utilisateurId, $nom, $prenom, $mdp, $role_utilisateur) {
		$requete = "insert into utilisateurs(id_user, nom, prenom, mdp, role_utilisateur, mdpGenere) values (?,?,?,?,?,?)";
		// Hacher le mot de passe généré, pour que l'utilisateur puisse s'identifier.
		$mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
		$tparam = array($utilisateurId, $nom, $prenom, $mdpHash, $role_utilisateur, $mdp);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Obtient tous les étudiants et tous les tuteurs.
	 * @return Utilisateur[] les étudiants et tuteurs.
	 */
	public function getEnseignantsTuteurs() {
		$requete = "select * from utilisateurs where role_utilisateur = 'E' or role_utilisateur= 'T' or role_utilisateur = 'AE' order by id_user";
		return $this->execQuery($requete, null, "Utilisateur");
	}


	// SEANCES

	/**
	 * Obtient toutes les séances créées pour un intervalle situé
	 * entre deux semaines, et filtre aussi en fonction des critères
	 * du module, du type de séance, du groupe et de l'enseignant.
	 * Pour ne pas filtrer selon ces critères, on peut donner la valeur "default".
	 * 
	 * @param string $semaineDebut La date de début.
	 * @param string $semaineFin La date de fin.
	 * @param string $module La valeur du module.
	 * @param string $type_seance Le type de la séance.
	 * @param string $groupe Le groupe concerné.
	 * @param string $proprietaire Le nom de l'utilisateur ayant créé la séance.
	 * @return Seance[] Les séances correspondant à ce filtre.
	 */
	public function getSeancesSemaine($semaineDebut, $semaineFin, $module, $typeSeance, $groupe, $proprietaire) {
		$requete = "select * from seance where date_seance >= ? and date_seance <= ?";
		$tparam = array($semaineDebut, $semaineFin);

		if($module != "default") {
			$requete =  $requete." and valeur_module = ?";
			$tparam[] = $module;
		}
		if($typeSeance != "default") {
			$requete =  $requete." and type_seance = ?";
			$tparam[] = $typeSeance;
		}
		if($groupe != "default") {
			$requete =  $requete." and groupe = ?";
			$tparam[] = $groupe;
		}
		if($proprietaire != "default") {
			$requete =  $requete." and id_user = ?";
			$tparam[] = $proprietaire;
		}

		$requete = $requete." order by date_seance ASC";
		return $this->execQuery($requete, $tparam, 'Seance');
	}

	/**
	 * Obtient toutes les séances sur un mois et une année donnée.
	 * @param string $mois Le mois.
	 * @param string $annee L'année.
	 * @return Seance[] Les séances créées sur le mois donné.
	 */
	public function getSeancesMois($mois,$annee){
		$requete = "select * from seance where date_seance LIKE ? order by date_seance ASC";
		$mois = sprintf("%02d", $mois+1);
		$tparam = array($annee.'-'.$mois.'-__%');
		return $this->execQuery($requete,$tparam,'Seance');
	}

	/**
	 * Obtient une séance à partir de toutes les informations qui la caractérisent
	 * @param string $valeur_module La valeur du module.
	 * @param string $date_seance La date de la séance (format ISO-8601).
	 * @param string $id_user Le créateur de la séance.
	 * @param string $type_seance Le type de la séance.
	 * @param string $groupe Le groupe concerné par la séance.
	 * @return Seance[] La séance correspondant aux paramètres fournis.
	 */
	public function getSeance($valeur_module, $date_seance, $id_user, $type_seance, $groupe){
		$requete = "select * from seance where valeur_module LIKE ? and date_seance LIKE ? and id_user LIKE ? and type_seance LIKE ? and groupe LIKE ? ORDER by date_seance ASC";
		$tparam = array($valeur_module, $date_seance, $id_user, $type_seance, $groupe);
		return $this->execQuery($requete, $tparam, 'Seance');
	}

	/**
	 * Crée une nouvelle séance avec les paramètres fournis.
	 * @param string $valeur_module La valeur du module.
	 * @param string $date_seance La date de la séance (format ISO-8601).
	 * @param string $id_user Le créateur de la séance.
	 * @param string $type_seance Le type de la séance.
	 * @param string $groupe Le groupe concerné par la séance.
	 * @return int 1 si la séance a été créée, 0 sinon.
	 */
	public function insertSeance($valeur_module, $date_seance, $id_user, $type_seance, $groupe){
		$requete = "insert into seance values(?,?,?,?,?)";
		$tparam = array($valeur_module, $date_seance, $id_user, $type_seance, $groupe);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Met à jour les informations d'une séance.
	 * @param array $old Les informations de la séance à modifier.
	 * @param array $new Les nouvelles informations.
	 * @return int 1 si la séance a été mise à jour, 0 sinon.
	 */
	public function updateSeance($old,$new){
		$requete = "update seance set valeur_module=?, date_seance=?, id_user=?, type_seance=?, groupe=? where
		valeur_module LIKE ? and date_seance LIKE ? and id_user LIKE ? and type_seance LIKE ? and groupe LIKE ?";
		return $this->execMaj($requete, array_merge($new, $old));
	}

	/**
	 * Supprime la séance qui correspond aux paramètres fournis.
	 * @param string $valeur_module La valeur du module.
	 * @param string $date_seance La date de la séance (format ISO-8601).
	 * @param string $id_user Le créateur de la séance.
	 * @param string $type_seance Le type de la séance.
	 * @param string $groupe Le groupe concerné par la séance.
	 * @return int 1 si la séance a été supprimée, 0 sinon.
	 */
	public function deleteSeance($valeur_module, $date_seance, $id_user, $type_seance, $groupe){
		$requete = "delete from seance where valeur_module=? and date_seance=?
		 and id_user=? and type_seance=? and groupe=?";
		 $tparam = array($valeur_module, $date_seance, $id_user, $type_seance, $groupe);
		return $this->execMaj($requete, $tparam);
	}

	// EVENEMENTS

	/**
	 * Obtient l'évènement grâce à son identifiant et une séance
	 * @param int $id_event L'identifiant de l'évènement.
	 * @param Seance $seance La séance qui contient (ou non) cet évènement.
	 * @return Evenement L'évènement correspondant aux paramètres fournis.
	 */
	public function getEvent($id_event, $seance){
		$requete = "select * from evenement where id_event = ? and valeur_module LIKE ? and date_seance LIKE ? and id_user LIKE ? and type_seance LIKE ? and groupe LIKE ?";
		$tparam = array($id_event, $seance->getValeurModule(), $seance->getDateSeance(),
			$seance->getIdUser(), $seance->getTypeSeance(), $seance->getGroupe());
		return $this->execQuery($requete, $tparam, 'Evenement');
	}

	/**
	 * Obtient tous les évènements correspondant à une séance.
	 * @param Seance $seance La séance qui contient les évènements.
	 * @return Evenement[] Tous les évènements de la séance.
	 */
	public function getEvents($seance){
		$requete = "select * from evenement where valeur_module = ? AND
		date_seance = ? AND id_user = ? AND type_seance = ? AND
		groupe = ? ORDER BY id_event";
		$tparam = array($seance->getValeurModule(), $seance->getDateSeance(),
		$seance->getIdUser(), $seance->getTypeSeance(), $seance->getGroupe());
		return $this->execQuery($requete, $tparam, 'Evenement');
	}

	/**
	 * Insère un nouvel évènement.
	 * @param array $new Les différents éléments constituant cet évènement.
	 * @return Evenement L'évènement inséré.
	 */
	public function insertEvent($new){
		$requete = "insert into evenement values(default,?,?,?,?,?,?,?,?,?)";
		$this->execMaj($requete, $new);
		$requete = "SELECT MAX(id_event) from evenement";
		return $this->execSimple($requete, array())[0];
	}

	/**
	 * Met à jour un évènement.
	 * @param array $new Les différents éléments de l'évènement à modifier.
	 * @return int (0 ou 1)
	 */
	public function updateEvent($new){
		$requete = "update evenement set type_event=?, lib_event=?, date_rendu=?, duree=? where id_event = ? and
		valeur_module LIKE ? and date_seance LIKE ? and id_user LIKE ? and type_seance LIKE ? and groupe LIKE ?";
		return $this->execMaj($requete, array_merge($new));
	}

	/**
	 * Supprime un évènement.
	 * @param int $idEvent L'identifiant unique de l'évènement.
	 * @return int 1 si l'évènement a été supprimé, 0 sinon.
	 */
	public function supprimerEvt($idEvent) {
		$requete = "delete from evenement where id_event =?";
		$tparam =  array($idEvent);
		return $this->execMaj($requete, $tparam);
	}

	// Groupes

	/**
	 * Obtient tous les groupes. 
	 * Trie les groupes d'abord en fonction de ceux qui ne possèdent pas de
	 * groupe père, puis en fonction de leur nombre de caractères
	 * (exemple : A avant A1, B après A, B avant A1)
	 * @return Groupe[] Tous les groupes.
	 */
	public function getGroupes() {
		$requete = "select * from groupes order by (groupepere like '') desc, length(groupe);";
		return $this->execQuery($requete, null, 'Groupe');
	}

	/**
	 * Modifie le nom d'un groupe.
	 * @param string $groupe Le nom du groupe à modifier.
	 * @param string $nouveau_groupe Le nouveau nom.
	 * @return int 1 si le groupe a été modifié, 0 sinon.
	 */
	public function modifGroupe($groupe, $nouveau_groupe) {
		$requete = "update groupes set groupe=? where groupe=?";
		$tparam = array($nouveau_groupe, $groupe);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Modifie le groupe père associé à un groupe.
	 * @param string $groupe Le groupe à modifier.
	 * @param string $nouveau_pere Le nouveau groupe père.
	 * @return int 1 si le groupe a été modifié, 0 sinon.
	 */
	public function modifGroupePere($groupe, $nouveau_pere) {
		$requete = "update groupes set groupepere=? where groupe=?";
		$tparam = array($nouveau_pere, $groupe);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Supprime un groupe. Les groupes seront supprimés en cascade.
	 * @param string $groupe Le groupe à supprimer.
	 * @return int 1 si le groupe a été supprimé, 0 sinon.
	 */
	public function supprGroupe($groupe) {
		$requete = "delete from groupes where groupe=?";
		$tparam = array($groupe);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Crée un nouveau groupe.
	 * @param string $groupe Le nom du groupe.
	 * @param string $groupePere Le groupe père du nouveau groupe.
	 * @return int 1 si le groupe a été créé, 0 sinon.
	 */
	public function creerGroupe($groupe, $groupePere) {
		$requete = "insert into groupes values (?,?)";
		$tparam = array($groupe, $groupePere);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Obtient un groupe en fonction de son nom.
	 * @param string $groupe Le nom du groupe à obtenir.
	 * @return Groupe Le tuple qui contient ce nom (le groupe et son père) sous forme d'objet.
	 */
	public function getGroupe($groupe){
		$requete = "select * from groupes where groupe = ?";
		$tparam = array($groupe);
		return $this->execQuery($requete, $tparam, 'Groupe');
	}

	/**
	 * Obtient tous les groupes affectés à un utilisateur.
	 * @param int $id_user L'identifiant de l'utilisateur.
	 * @return Groupe[] Tous les groupes affectés à l'utilisateur.
	 */
	public function getGroupesAffectes($id_user){
		$requete = "select * from affectationgroupestuteurs where id_user = ?";
		$tparam = array($id_user);
		return $this->execQuery($requete, $tparam, 'Groupe');
	}

	// Modules

	/**
	 * Obtient le nom du module (le libellé) à partir de sa valeur.
	 * @param string $valeur_module La valeur du module.
	 * @return string Le libellé du module.
	 */
	public function getNomModule($valeur_module) {
		$requete = "select lib_module from modules where valeur_module=?";
		$tparam = array($valeur_module);
		return $this->execSimple($requete, $tparam);
	}

	/**
	 * Obtient tous les modules, triés selon l'ordre alphabétique de
	 * la valeur des modules.
	 * @return Module[] Tous les modules.
	 */
	public function getModules() {
		$requete = "select * from modules order by valeur_module ASC";
		return $this->execQuery($requete, null, 'Module');
	}

	/**
	 * Obtient un module grâce à sa valeur.
	 * @param string $valeur_module La valeur du module.
	 * @return Module Le module voulu.
	 */
	public function getModule($valeur_module){
		$requete = "select * from modules where valeur_module=?";
		$tparam = array($valeur_module);
		return $this->execQuery($requete, $tparam, 'Module')[0];
	}

	/**
	 * Obtient tous les modules qui sont affectés à un utilisateur.
	 * @param string $id_user L'identifiant de l'utilisateur.
	 * @return Module[] Les modules affectés à l'utilisateur.
	 */
	public function getModulesByUser($id_user){
		$requete = "select * from modules where valeur_module in (select valeur_module from affectationsmodules where id_user LIKE ?)";
		$tparam = array($id_user);
		return $this->execQuery($requete, $tparam, 'Module');
	}

	/**
	 * Supprime un module.
	 * @param string $module La valeur du module.
	 * @return int 1 si le module a été supprimé, 0 sinon.
	 */
	public function supprModule($module) {
		$requete = "delete from modules where valeur_module=?";
		$tparam = array($module);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Teste si un utilisateur est affecté à un module.
	 * @param string $id_user L'identifiant de l'utilisateur.
	 * @param string $valeur_module La valeur du module.
	 * @return bool true si l'utilisateur est affecté à ce module, false sinon.
	 */
	public function verifUtilAModule($id_user,$valeur_module){
		$requete = "select * from affectationsModules where id_user=? and valeur_module=?";
		$tparam = array($id_user, $valeur_module);
		return !empty($this->execSimple($requete, $tparam));
	}

	/**
	 * Obtient la couleur d'un module.
	 * @param string $valeur_module La valeur du module.
	 * @return string La couleur du module au format RGB, sans préfixe.
	 */
	public function coulModule($valeur_module){
		$requete = "select couleur from modules where valeur_module=?";
		return $this->execSimple($requete, array($valeur_module))[0];
	}

	/**
	 * Ajoute un module.
	 * @param string $val La valeur du module.
	 * @param string $lib Le libellé du module.
	 * @param string $col2 La couleur du module.
	 * @param string $droit Le rôle requis pour accéder au module.
	 * @return int 1 si le module a été créé, 0 sinon.
	 */
	public function addModule($val, $lib, $col2, $droit){
		$requete = "insert into modules values (?,?,?,?)";
		$tparam = array($val, $lib, $col2, $droit);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Modifie la valeur du module.
	 * @param string $nouvValeur La nouvelle valeur du module.
	 * @param string $valeur_module La valeur actuelle du module.
	 * @return int 1 si la modification a été effectuée, 0 sinon.
	 */
	public function modifModuleValeur($nouvValeur, $valeur_module) {
		$requete =  "update modules set valeur_module=? where valeur_module=?";
		$tparam =  array($nouvValeur, $valeur_module);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Modifie le libellé du module.
	 * @param string $nouvLib Le nouveau libellé du module.
	 * @param string $valeur_module La valeur du module à modifier.
	 * @return int 1 si la modification a été effectuée, 0 sinon.
	 */
	public function modifModuleLib($nouvLib, $valeur_module) {
		$requete =  "update modules set lib_module=? where valeur_module=?";
		$tparam =  array($nouvLib, $valeur_module);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Modifie la couleur du module.
	 * @param string $nouvCoul La nouvelle couleur du module.
	 * @param string $valeur_module La valeur du module à modifier.
	 * @return int 1 si la modification a été effectuée, 0 sinon.
	 */
	public function modifModuleCoul($nouvCoul, $valeur_module) {
		$requete =  "update modules set couleur=? where valeur_module=?";
		$tparam =  array($nouvCoul, $valeur_module);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Modifie le droit du module.
	 * @param string $nouvDroit Le nouveau droit du module.
	 * @param string $valeur_module La valeur du module à modifier.
	 * @return int 1 si la modification a été effectuée, 0 sinon.
	 */
	public function modifModuleDroit($nouvDroit, $valeur_module) {
		$requete =  "update modules set droit=? where valeur_module=?";
		$tparam =  array($nouvDroit, $valeur_module);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Obtient les utilisateurs affectés à un module.
	 * @param string $valeur_module La valeur du module.
	 * @return array Les utilisateurs affectés au module.
	 */
	public function getAffectations($valeur_module) {
		$requete = "select id_user from affectationsmodules where valeur_module = ?";
		$tparam = array($valeur_module);
		$tabTemp = $this->multiExecSimple($requete, $tparam);

		$tab = array();
		foreach($tabTemp as $id) {
			$tab[] = $this->getUtilisateur($id[0])[0];
		}

		return $tab;
	}

	/**
	 * Affecte un utilisateur à un module.
	 * @param string $id_user L'identifiant de l'utilisateur pour qui affecter le module.
	 * @param string $valeur_module La valeur du module à affecter.
	 * @return int 1 si le module a été affecté, 0 sinon.
	 */
	public function insertAffectModules($id_user, $valeur_module){
		$requete = "insert into affectationsmodules values(?,?)";
		$tparam = array($id_user, $valeur_module);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Supprime l'affectation d'un utilisateur à un module.
	 * @param string $id_user L'identifiant de l'utilisateur.
	 * @param string $valeur_module La valeur du module.
	 * @return int 1 si l'affectation a été supprimée, 0 sinon.
	 */
	public function deleteAffectModules($id_user, $valeur_module){
		$requete = "delete from affectationsmodules where id_user=? and valeur_module=?";
		$tparam = array($id_user, $valeur_module);
		return $this->execMaj($requete, $tparam);
	}

	//TUTEURS

	/**
	 * Obtient tous les tuteurs.
	 * @return Utilisateur[] Tous les tuteurs.
	 */
	public function getTuteurs() {
		$requete = "select * from utilisateurs where role_utilisateur='T'";
		return $this->execQuery($requete, null, 'Utilisateur');
	}

	/**
	 * Affecte un tuteur à un groupe.
	 * @param string $tuteur L'identifiant du tuteur.
	 * @param string $groupe Le groupe.
	 * @return int 1 si le groupe a été affecté au tuteur, 0 sinon.
	 */
	public function affecterTuteurGroupe($tuteur, $groupe) {
		$requete = "insert into affectationgroupestuteurs values (?,?)";
		$tparam = array($tuteur, $groupe);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Supprime l'affectation d'un tuteur à un groupe.
	 * @param string $tuteur L'identifiant du tuteur.
	 * @param string $groupe Le groupe.
	 * @return int 1 si l'affection a été supprimée, 0 sinon.
	 */
	public function supprimerTuteurGroupe($tuteur, $groupe) {
		$requete = "delete from affectationgroupestuteurs where id_user=? and groupe=?";
		$tparam = array($tuteur, $groupe);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Obtient tous les tuteurs ainsi que leurs groupes tutorés.
	 * @param TuteurGroupe[] Les affectations des tuteurs aux groupes.
	 */
	public function getTuteursGroupes() {
		$requete = "select * from affectationgroupestuteurs";
		return $this->execQuery($requete, null, 'TuteurGroupe');
	}

	//CONTRAINTES

	/**
	 * Obtient toutes les contraintes.
	 * @return Contrainte[] Les contraintes.
	 */
	public function getContraintes(){
		$requete = "select * from contraintes";
		return $this->execQuery($requete, null, 'Contrainte');
	}

	/**
	 * Obtient le nombre maximum d'évènements pour une séance.
	 * @return int Le nombre maximum d'évènements.
	 */
	public function getMaxEvtParAct(){
		$requete = "select valeur from contraintes where tab='seance' and type='nombre'";
		return $this->execSimple($requete,null)[0];
	}

	/**
	 * Obtient le nombre maximum de séances affichables sur un page,
	 * lorsqu'on filtre des séances.
	 * @return int Le nombre maximum de séances affichables.
	 */
	public function getMaxSeancesAffichables() {
		$requete = "select valeur from contraintes where tab='seance' and type='affichageNombre'";
		return $this->execSimple($requete, null)[0];
	}

	/**
	 * Obtient le nombre maximum de pièces jointes pouvant être attachées
	 * à un évènement.
	 * @return int Le nombre maximum de pièces jointes.
	 */
	public function getMaxPieceJointe(){
		$requete = "select valeur from contraintes where tab='evenement' and type='nombre'";
		return $this->execSimple($requete,null)[0];
	}

	/**
	 * Modifie le nombre maximum d'évènements par séance.
	 * @param mixed $newValeur La nouvelle valeur de la contrainte.
	 * @return int 1 si la valeur a été modifiée, 0 sinon.
	 */
	public function modifierNbMaxEvtParAct($newValeur) {
		$requete = "update contraintes set valeur = ? where tab='seance' and type='nombre'";
		$tparam = array($newValeur);
		return $this->execMaj($requete,$tparam);
	}

	/**
	 * Modifie le nombre maximum de séances affichables.
	 * @param mixed $newValeur La nouvelle valeur de la contrainte.
	 * @return int 1 si la valeur a été modifiée, 0 sinon.
	 */
	public function modifierNbMaxSeancesAfficheables($newValeur) {
		$requete = "update contraintes set valeur = ? where tab='seance' and type='affichageNombre'";
		$tparam = array($newValeur);
		return $this->execMaj($requete,$tparam);
	}

	/**
	 * Modifie le nombre maximum de pièces jointes pour un évènement.
	 * @param mixed $newValeur La nouvelle valeur de la contrainte.
	 * @return int 1 si la valeur a été modifiée, 0 sinon.
	 */
	public function modifierNbMaxPieceJointes($newValeur) {
		$requete = "update contraintes set valeur = ? where tab='evenement' and type='nombre'";
		$tparam = array($newValeur);
		return $this->execMaj($requete,$tparam);
	}

	/**
	 * Supprime un type de séance.
	 * @param string $valASupprimer La valeur à supprimer.
	 * @return int 1 si la valeur a été supprimée, 0 sinon.
	 */
	public function supTypeSeance($valASupprimer) {
		$requete = "delete from contraintes where valeur=? and tab='seance' and type='type' ";
		$tparam = array($valASupprimer);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Ajoute un type de séance.
	 * @param string $valAAjouter La valeur à ajouter.
	 * @return int 1 si la valeur a été ajoutée, 0 sinon.
	 */
	public function addTypeSeance($valAAjouter) {
		$requete = "insert into contraintes values ('seance', 'type', ?)";
		$tparam = array($valAAjouter);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Supprime un type d'évènement.
	 * @param string $valASupprimer La valeur à supprimer.
	 * @return int 1 si la valeur a été supprimée, 0 sinon.
	 */
	public function supTypeEvt($valASupprimer) {
		$requete = "delete from contraintes where valeur=? and tab='evenement' and type='type' ";
		$tparam = array($valASupprimer);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Ajoute un type d'évènement.
	 * @param string $valAAjouter La valeur à ajouter.
	 * @return int 1 si la valeur a été ajoutée, 0 sinon.
	 */
	public function addTypeEvt($valAAjouter) {
		$requete = "insert into contraintes values ('evenement', 'type', ?)";
		$tparam = array($valAAjouter);
		return $this->execMaj($requete, $tparam);
	}

	/**
	 * Modifie un type de séance.
	 * @param string $newval La nouvelle valeur.
	 * @param string $oldval L'ancienne valeur.
	 * @return int 1 si la valeur a été modifiée, 0 sinon.
	 */
	public function modifTypeS($newval,$oldval) {
		$requete = "update contraintes set valeur=? where valeur=? and tab='seance'";
		$tparam = array($newval,$oldval);
		return $this->execMaj($requete,$tparam);
	}

	/**
	 * Modifie un type d'évènement.
	 * @param string $newval La nouvelle valeur.
	 * @param string $oldval L'ancienne valeur.
	 * @return int 1 si la valeur a été modifiée, 0 sinon.
	 */
	public function modifTypeE($newval, $oldval) {
		$requete = "update contraintes set valeur=? where valeur=? and tab='evenement'";
		$tparam = array($newval,$oldval);
		return $this->execMaj($requete,$tparam);
	}

	//UTILISATEUR SEANCE

	/**
	 * Ajoute un sémaphore pour l'utilisateur.
	 * @param string $module La valeur du module correspondant à la séance.
	 * @param string $date La date de la séance.
	 * @param string $user L'identifiant de l'utilisateur à qui appartient la séance.
	 * @param string $type Le type de la séance.
	 * @param string $groupe Le groupe concernant la séance.
	 * @param string $utilis L'identifiant de l'utilisateur pour lequel ajouter le sémaphore.
	 * @return int 1 si le sémaphore a été ajouté, 0 sinon.
	 */
	public function ajouterSemaphore($module,$date,$user,$type,$groupe,$utilis){
		$requete = "insert into utilisateurseance values (?,?,?,?,?,?,true)";
		$tparam = array($module,$date,$user,$type,$groupe,$utilis);
		return $this->execMaj($requete,$tparam);
	}

	/**
	 * Supprime un sémaphore.
	 * @param string $module La valeur du module correspondant à la séance.
	 * @param string $dateseance La date de la séance.
	 * @param string $user L'identifiant de l'utilisateur à qui appartient la séance.
	 * @param string $typeseance Le type de la séance.
	 * @param string $groupe Le groupe concernant la séance.
	 * @param string $utilis L'identifiant de l'utilisateur pour lequel ajouter le sémaphore.
	 * @return int 1 si le sémaphore a été supprimé, 0 sinon.
	 */
	public function supprimerSemaphore($module,$dateseance,$user,$typeseance,$groupe,$utilis) {
		$requete = "delete from utilisateurseance where valeur_module=? and date_seance=? and id_user=? and type_seance=? and groupe=? and utilisateur=?";
		$tparam = array($module,$dateseance,$user,$typeseance,$groupe,$utilis);
		return $this->execMaj($requete,$tparam);
	}

	/**
	 * Obtient les sémaphores.
	 * @return Utilisateurseance[] Les sémaphores.
	 */
	public function getSemaphore() {
		$requete = "select * from utilisateurseance";
		return $this->execQuery($requete, null, 'Utilisateurseance');
	}

	/**
	 * Change la couleur pour les deux états d'un sémaphore.
	 * @param string $etat L'état du sémaphore.
	 * @param string $nonVu La couleur du sémaphore "non vu".
	 * @param string $vu La couleur du sémaphore "vu".
	 * @return int 1 si la couleur du sémaphore a été modifiée, 0 sinon.
	 */
	public function changerCouleur($etat, $nonVu, $vu) {
		$requete = "update semaphore set couleurnonvu=?, couleurvu=? where etat LIKE ?";
		$tparam = array($nonVu, $vu, $etat);
		return $this->execMaj($requete,$tparam);
	}

	/**
	 * Obtient la couleur du bouton de l'état 1 "vu".
	 * @return Semaphore la couleur de l'état.
	 */
	public function getCoulBoutEtat1() {
		$requete = "select couleurvu from semaphore where etat LIKE 'vu'";
		return $this->execQuery($requete, null, 'Semaphore');
	}

	/**
	 * Obtient la couleur du bouton de l'état 2 "non vu".
	 * @return Semaphore la couleur de l'état.
	 */
	public function getCoulBoutEtat2() {
		$requete = "select couleurnonvu from semaphore where etat LIKE 'vu'";
		return $this->execQuery($requete, null, 'Semaphore');
	}

	/**
	 * Obtient la couleur du texte de l'état 1 "vu".
	 * @return Semaphore la couleur de l'état.
	 */
	public function getCoulTxtEtat1() {
		$requete = "select couleurvu from semaphore where etat LIKE 'texte'";
		return $this->execQuery($requete, null, 'Semaphore');
	}

	/**
	 * Obtient la couleur du texte de l'état 2 "non vu".
	 * @return Semaphore la couleur de l'état.
	 */
	public function getCoulTxtEtat2() {
		$requete = "select couleurnonvu from semaphore where etat LIKE 'texte'";
		return $this->execQuery($requete, null, 'Semaphore');
	}

	// PIECES JOINTES

	/**
	 * Crée une nouvelle entrée de pièce jointe.
	 * @param array $new Les informations sur cette pièce jointe.
	 * @return string Le nom aléatoire de la pièce jointe créée.
	 */
	public function insertPj($new){
		$requete = "INSERT INTO pieces_jointes values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
		// Créer un identifiant unique pour le nom de cette pièce jointe.
		// Permet d'avoir plusieurs fichiers avec le même nom lorsque plusieurs utilisateurs
		// envoient une pièce jointe.
		$nb = md5(uniqid());
		$tparam = array_merge(array($nb), $new);

		// Date au format YYYY-mm correspondant au dossier où sera stocké le fichier.
		$tparam[] = date('Y-m');
 		$this->execMaj($requete, $tparam);
		return $nb;
	}

	/**
	 * Obtient des pièce jointes.
	 * @param array $event L'évènement auquel appartient la pièce jointe.
	 * @return PiecesJointes[] Les pièces jointes associées à l'évènement.
	 */
	public function getPj($event){
		$requete = "SELECT * from pieces_jointes where id_event = ? and valeur_module LIKE ? and date_seance LIKE ? and id_user LIKE ? and type_seance LIKE ? and groupe LIKE ?";
		return $this->execQuery($requete, $event, "PiecesJointes");
	}

	/**
	 * Supprime une pièce jointe.
	 * Le fichier n'est pas supprimé des documents stockés par le serveur.
	 * @param string $nomPj Le md5 de la pièce jointe à supprimer.
	 * @return int 1 si la pièce jointe a été supprimée, 0 sinon.
	 */
	public function deletePj($nomPj){
		$requete = "DELETE FROM pieces_jointes where nomFichier LIKE ?";
		return $this->execMaj($requete, array($nomPj));
	}
}
?>
