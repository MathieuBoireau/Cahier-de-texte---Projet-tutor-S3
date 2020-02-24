<?php

	include_once "DB.inc.php";

	session_start();
	if (!isset($_SESSION['utilisateur'])) {
		header("Location: login.php");
		exit(1);
	}

	//Récupère les informations du ajax
	//myFunction désigne la fonction à appeller
	//params sont les paramètres de la fonction
	if (isset( $_REQUEST['myFunction']) && $_REQUEST['myFunction'] != '')
		$_REQUEST['myFunction']($_REQUEST);

	/**
	 * Modifie tous les groupes envoyés par ajax
	 * @param $params
	 */
	function modifGroupes($params) {
		$db = DB::getInstance();
		$listeModif = json_decode($params["param"]["listeModif"]);
		//Récupération des groupes déjà présents dans la base de donnée
		$ancienGroupes = $db->getGroupes();

		for ($i = 0; $i < count($ancienGroupes); $i++) {

			//Maj du groupe père
			$nouvGroupePere = $listeModif[1][$i];
			if($listeModif[1][$i] != "none")
			 	$nouvGroupePere = $listeModif[1][$i];
			else
				$nouvGroupePere =  null;
			if($nouvGroupePere != $ancienGroupes[$i]->getGroupePere())
				$db->modifGroupePere($ancienGroupes[$i]->getGroupe(), $nouvGroupePere);

			// Maj du groupe
			if($listeModif[0][$i] != $ancienGroupes[$i]->getGroupe())
				$db->modifGroupe($ancienGroupes[$i]->getGroupe(), $listeModif[0][$i]);
		}
	}

	function supprGroupe($params) {
		$db = DB::getInstance();
		$groupe = json_decode($params["param"]["groupe"]);
		$db->supprGroupe($groupe);
	}

	function creerGroupe($params) {
		$db = DB::getInstance();
		$nouvGroupe = json_decode($params["param"]["nouvGroupe"]);
		if($nouvGroupe[1]=="")
			$nouvGroupe[1] = null;
		if ($nouvGroupe[0] != "")
			$db->creerGroupe($nouvGroupe[0], $nouvGroupe[1]);
	}

	function modifModules($params) {
		$db = DB::getInstance();
		$listeModif = json_decode($params["param"]["listeModif"]);
		$ancienModule =  $db->getModules()[$listeModif[4]];

		if($listeModif[1] != $ancienModule->getLibModule())
			$db->modifModuleLib($listeModif[1], $ancienModule->getValeurModule());

		// Maj de la couleur du module
		// Formatage de la valeur pour qu'elle puisse concordonner avec les données dans la base de données
		// et gestion de la non présence de valeur dans la base de données qui correspond à aucune couleur / transparent
		$nouvCoul = str_replace("#", "", strtoupper($listeModif[2]));

		// La couleur 000000 correspond à transparent
		if ($nouvCoul == "000000")
			$nouvCoul = "";
		$db->modifModuleCoul($nouvCoul, $ancienModule->getValeurModule());

		//Maj des droits du module
		if($listeModif[3] != $ancienModule->getDroit())
			$db->modifModuleDroit($listeModif[3], $ancienModule->getValeurModule());

		// Maj de la valeur du module
		if($listeModif[0] != $ancienModule->getValeurModule())
			$db->modifModuleValeur($listeModif[0], $ancienModule->getValeurModule());
	}

	function modifAffectModules($params) {
		$db = DB::getInstance();
		$listeModif = json_decode($params["param"]["listeModif"]);
		$module =  $db->getModules()[$listeModif[count($listeModif)-1]];

		// Affectation
		$tabUtilAffectes = getUtilAffectes($module,$db);

		for($i=0; $i<count($listeModif)-1; $i++)
			if(!in_array($listeModif[$i], $tabUtilAffectes))
				$db->insertAffectModules($listeModif[$i], $module->getValeurModule());

		// Désaffectation
		$tabUtilAffectes = getUtilAffectes($module,$db);

		foreach($tabUtilAffectes as $utilAffecte)
			if(!in_array($utilAffecte, $listeModif))
				$db->deleteAffectModules($utilAffecte, $module->getValeurModule());
	}

	/**
	 * Récupère tous les utilisateurs associés à un module
	 * @param $module
	 * @param $db
	 * @return array
	 */
	function getUtilAffectes($module, $db){
		$tabUtilAffectes = array();
		foreach($db->getAffectations($module->getValeurModule()) as $util){
			array_push($tabUtilAffectes, $util->getId());
		}
		return $tabUtilAffectes;
	}

	function supprModule($params) {
		$db = DB::getInstance();
		$module = json_decode($params["param"]["module"]);
		$db->supprModule($module);
	}

	function ajouterModule($params) {
		$db = DB::getInstance();
		$valeur   = json_decode($params["param"]["valeur"]);
		$libel    = json_decode($params["param"]["libel"]);
		$color    = json_decode($params["param"]["color"]);
		$aAjouter = json_decode($params["param"]["aAjouter"]);
		// Ne pas autoriser la couleur noir (#000000) et la remplacer par aucune couleur
		if ($color == "000000")
			$color = "";
		// Ne pas ajouter le module si il n'y a pas de valeurs correctes fournies
		if ($valeur != "" && $libel != "")
			$db->addModule($valeur,$libel,$color,$aAjouter);
	}


	// Séances
	function updateSeance($params){
		$initSeance = json_decode($params["params"]["initSeance"]);
		$seance = json_decode($params["params"]["seance"]);
		$evenements = json_decode($params["params"]["evenements"]);
		$db = DB::getInstance();
		for($i = 0; $i < count($initSeance); $i++){
			if(strcmp($initSeance[$i], $seance[$i]) != 0){//Si un paramètre de la séance est modifié, fait appel à updateSeance de db
				$db->updateSeance($initSeance, $seance);
				break;
			}
		}
		$affectationsEventPj = array(); //Tableau associant un nom de pièce-jointe à un id unique envoyé par la base de donnée
		for($i = 0; $i < count($evenements); $i++){
			if(strcmp($evenements[$i][4], "+") == 0){//Si l'événement est un nouvel événement ou un événement à modifier
				//Ajoute dans le tableau l'id de l'événement qui vient d'être inséré (pour les pièces-jointes)
				$evenements[$i][4] = $db->insertEvent(array_merge($seance, array_slice($evenements[$i], 0, count($evenements[$i])-2)));
			}
			else
				$db->updateEvent(array_merge(array_slice($evenements[$i], 0, 5), $seance));
			$nameArray = array();//Attributs les noms des pièces-jointes d'un événement à leur id unique
			for($j = 0; $j < count($evenements[$i][5]); $j++){
				$nameArray[$evenements[$i][5][$j]] = $db->insertPj(array_merge(array($evenements[$i][5][$j]), array($evenements[$i][4]),$seance));
			}
			$affectationsEventPj[] = $nameArray; //Regroupe tous les tableaux associant nom et id dans un seul tableau
		}
		echo json_encode($affectationsEventPj); //Envoie ce tableau convertit en JSON au javascript
	}

	/**
	 * Ajoute une séance et son événement
	 * @param $params
	 */
	function addSeance($params){
		$seance = json_decode($params["params"]["seance"]);
		$evenement = json_decode($params["params"]["evenement"]);
		$db = DB::getInstance();
		$db->insertSeance($seance[0], $seance[1], $seance[2], $seance[3], $seance[4]);
		$evenement[0][4] =  $db->insertEvent(array_merge($seance, array_slice($evenement[0], 0, count($evenement[0])-2)));
		$nameArray = array();
		for($j = 0; $j < count($evenement[0][5]); $j++){
			$nameArray[$evenement[0][5][$j]] = $db->insertPj(array_merge(array($evenement[0][5][$j]), array($evenement[0][4]),$seance));
		}
		$affectationsEventPj[] = $nameArray;
		echo json_encode($affectationsEventPj);
	}

	function deleteSeance($params){
		$db = DB::getInstance();
		$seance = explode("|", $params["params"]["suppr"]);
		$db->deleteSeance($seance[0], $seance[1], $seance[2], $seance[3], $seance[4]);
	}

	/**
	 * Vérifie si une séance existe déjà
	 * echo "false" s'il existe déjà, "true" sinon
	 * @param $params
	 */
	function verifSeance($params){
		$db = DB::getInstance();
		$seance = json_decode($params["params"]["seance"]);
		if(array_key_exists(0, $db->getSeance($seance[0], $seance[1], $seance[2], $seance[3], $seance[4])))
			echo "false";
		else
			echo "true";
	}

	/**
	 * Supprime une association entre un groupe et un tuteur
	 * @param $params
	 */
	function supprTutGroupe($params) {
		$p1 = json_decode($params["param"]["p1"]);
		$p2 = json_decode($params["param"]["p2"]);
		$db = DB::getInstance();
		$db->supprimerTuteurGroupe($p1,$p2);
	}

	function supprTypeSeance($params) {
		$type = json_decode($params["param"]["type"]);
		$db = DB::getInstance();
		$db->supTypeSeance($type);
	}

	function ajoutTypeSeance($params) {
		$type = json_decode($params["param"]["type"]);
		$db = DB::getInstance();
		if ($type != "")
			$db->addTypeSeance($type);
	}

	function supprTypeEvt($params) {
		$type = json_decode($params["param"]["type"]);
		$db = DB::getInstance();
		$db->supTypeEvt($type);
	}

	function ajoutTypeEvt($params) {
		$type = json_decode($params["param"]["type"]);
		$db = DB::getInstance();
		if ($type != "")
			$db->addTypeEvt($type);
	}

	function modifierTypeSeance($params) {
		$old = json_decode($params["param"]["old"]);
		$new = json_decode($params["param"]["new"]);
		$db = DB::getInstance();
		if ($new != "")
			$db->modifTypeS($new,$old);
	}

	function modifierTypeEvt($params) {
		$old = json_decode($params["param"]["old"]);
		$new = json_decode($params["param"]["new"]);
		$db = DB::getInstance();
		if ($new != "")
			$db->modifTypeE($new,$old);
	}

	function supprimerEvt($params) {
		$idEvent = json_decode($params["param"]["idEvent"]);
		$db = DB::getInstance();
		$db->supprimerEvt($idEvent);
	}



	/**
	 * Crée/Supprime une association entre la séance et l'utilisateur connecté qui clique sur le bouton sur la page etatSeances.php
	 * S'il ajoute, le sémaphore est considéré comme vrai (c'est-à-dire "Vu")
	 * @param $params
	 */
	function ajouterSemaphoreVrai($params) {
		// echo "ajout";
		$module = json_decode($params["param"]["module"]);
		$groupe = json_decode($params["param"]["groupe"]);
		$dateseance = json_decode($params["param"]["dateseance"]);
		$typeseance   = json_decode($params["param"]["typeseance"]);
		$user   = json_decode($params["param"]["user"]);
		$utilis = json_decode($params["param"]["utilis"]);
		$db = DB::getInstance();
		$db->ajouterSemaphore($module,$dateseance,$user,$typeseance,$groupe,$utilis);
	}

	function supprimerSemaphoreVrai($params) {
		echo "suppression";
		$module = json_decode($params["param"]["module"]);
		$groupe = json_decode($params["param"]["groupe"]);
		$dateseance = json_decode($params["param"]["dateseance"]);
		$typeseance   = json_decode($params["param"]["typeseance"]);
		$user   = json_decode($params["param"]["user"]);
		$utilis = json_decode($params["param"]["utilis"]);
		$db = DB::getInstance();
		echo "Ma ligne qui va être suppr : ".$module." ".$dateseance." ".$user." ".$typeseance." ".$groupe." ".$utilis;
		$db->supprimerSemaphore($module,$dateseance,$user,$typeseance,$groupe,$utilis);
	}

	function changerCouleurSemaphores($params) {
		$db = DB::getInstance();
		$semaphores = json_decode($params["params"]["semaphores"]);
		for($i = 0; $i < count($semaphores); $i++){
			$db->changerCouleur($semaphores[$i][0],$semaphores[$i][1],$semaphores[$i][2]);
		}
	}

	//Piece-jointe
	function deletePj($params){
		$db = DB::getInstance();
		$db->deletePj($params["params"]["nomPj"]);
	}

?>
