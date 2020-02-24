<?php
	require "DB.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";
	include_once "fctAux.inc.php";
	
	$utilisateur = getStart();

	contenu();

	function contenu() {
		// Trouver le mois à afficher.
		// S'il n'est pas spécifié, on utilise le mois courant.
		if(isset($_REQUEST["mois"]) && ctype_digit($_REQUEST["mois"]))
			$mois = $_REQUEST["mois"];
		else
			$mois = date('m')-1;

		// Même fonctionnement pour l'année.
		if(isset($_REQUEST["annee"]) && ctype_digit($_REQUEST["annee"]))
			$annee = $_REQUEST["annee"];
		else
			$annee = date('Y');

		// Pour éviter qu'un étudiant puisse accéder à une autre année via l'url
		if(!periodeAccessible($mois, $annee)){
			$mois = date('m')-1;
			$annee = date('Y');
		}

		Twig_Autoloader::register();
		$twig = new Twig_Environment( new Twig_Loader_Filesystem("./tpl"));

		// Début
		global $utilisateur;
		$db = DB::getInstance();

		$tpl = $twig->loadTemplate( "journalDeBord.tpl" );

		if(isset($_REQUEST["add"])){
			if($_REQUEST["lib"] != "" and !($_REQUEST["typeEvent"] == "Travail à faire" and $_REQUEST["daterendu"] == "")){
				/*if($_REQUEST["daterendu"] == "" or $_REQUEST["typeEvent"] == "Travail fait")
					$_REQUEST["daterendu"] = null;*/
				if($_REQUEST["duree"] == "")
					$_REQUEST["duree"] = null;
				$db->insertEvent(array($_REQUEST["module"], $_REQUEST["date"], $_REQUEST["user"], $_REQUEST["typeSeance"], $_REQUEST["groupe"], $_REQUEST["typeEvent"], $_REQUEST["lib"], $_REQUEST["daterendu"], $_REQUEST["duree"]));
			}
		}


		// Affichage de la page.
		echo $tpl->render(array(
			"titre"  =>"Journal de bord",
			"css"    => array("styleJournalDeBord"),
			"flecheG"=>url('G',$mois,$annee),
			"flecheD"=>url('D',$mois,$annee),
			"mois"   =>getNomMois($mois),
			"annee"  =>$annee,
			"seances"=>$db->getSeancesMois($mois,$annee),
			"user"   =>$utilisateur,
			"url"    =>"journalDeBord.php?mois=".$mois."&annee=".$annee,
			"contraintes" => $db->getContraintes()
		));
	}

	/**
	 * Crée l'extension de l'URL, avec les paramètres mois et année.
	 * Crée aussi les URL pour les flèches permettant d'aller aux mois
	 * suivant et précédent.
	 * @param string $gaucheDroite G|D|''
	 * @param mixed $mois Le mois courant dans la page.
	 * @param mixed $annee L'année courante dans la page.
	 * @return string Le suffixe de l'URL pour les paramètres donnés.
	 */
	function url($gaucheDroite,$mois,$annee){
		$modif = array('G' => -1, 'D' => 1, '' => 0);
		$mois = ($mois + $modif[$gaucheDroite]);
		$fleche="";
		if(periodeAccessible($mois,$annee)){
			$fleche = "journalDeBord.php?mois=".(($mois + 12) % 12);

			if ($mois == -1 && $gaucheDroite == 'G' || $mois == 12 && $gaucheDroite == 'D') {
				$annee = $annee + $modif[$gaucheDroite];
			}
			$fleche = $fleche."&annee=".$annee;
		}

		return $fleche;
	}

	/**
	 * Vérifie que la période est accessible par l'utilisateur.
	 * Par exemple, un tuteur n'a accès qu'à la période correspondant
	 * à septembre de l'année courante jusqu'à juin de l'année suivante.
	 * @param mixed $mois Le mois courant de la page.
	 * @param mixed $annee L'année courante dans la page.
	 * @return bool true si l'utilisateur a accès à cette période, false sinon.
	 */
	function periodeAccessible($mois,$annee){
		global $utilisateur;
		return $utilisateur->getRole()!="T"                          ||
			   ($mois+1)>=9 && $annee==date('Y',strtotime($utilisateur->getCreation()))   ||
			   ($mois+1)<=6 && $annee==date('Y',strtotime($utilisateur->getCreation()))+1;
	}
?>