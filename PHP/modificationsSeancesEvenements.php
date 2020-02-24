<?php

	include "fctAux.inc.php";
	getStartAdmin();
	require_once "DB.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";

	Twig_Autoloader::register();
	$twig = new Twig_Environment( new Twig_Loader_Filesystem("./tpl"));

	$db = DB::getInstance();

	// Modification en base de données si le formulaire a été rempli.
	if(isset($_REQUEST["nbMaxEvtParAct"]) && $_REQUEST["nbMaxEvtParAct"]!="") {
		$db->modifierNbMaxEvtParAct($_REQUEST["nbMaxEvtParAct"]);
	}

	if(isset($_REQUEST["nbMaxPieceJointes"]) && $_REQUEST["nbMaxPieceJointes"]!="") {
		$db->modifierNbMaxPieceJointes($_REQUEST["nbMaxPieceJointes"]);
	}

	if(isset($_REQUEST["nbMaxSeanceAff"]) && $_REQUEST["nbMaxSeanceAff"]!="") {
		$db->modifierNbMaxSeancesAfficheables($_REQUEST["nbMaxSeanceAff"]);
	}

	// Affichage de la page.
	$couleurBoutonEtat1 = $db->getCoulBoutEtat1()[0];
	$couleurTexteEtat1   = $db->getCoulTxtEtat1()[0];
	$couleurBoutonEtat2 = $db->getCoulBoutEtat2()[0];
	$couleurTexteEtat2  = $db->getCoulTxtEtat2()[0];

	$tpl = $twig->loadTemplate( "modificationsSeancesEvenements.tpl" );
	echo $tpl->render(array(
		"titre"  =>"Modification Séances et Événements",
		"css" => array("styleModificationsSeancesEvenements"),
		"maxEvtParAct" => $db->getMaxEvtParAct(),
		"maxPieceJointe" => $db->getMaxPieceJointe(),
		"maxSeanceAff" => $db->getMaxSeancesAffichables(),
		"contraintes" => $db->getContraintes(),
		"couleurBoutonEtat1" => $couleurBoutonEtat1[0],
		"couleurTexteEtat1"  => $couleurTexteEtat1[0],
		"couleurBoutonEtat2" => $couleurBoutonEtat2[0],
		"couleurTexteEtat2"  => $couleurTexteEtat2[0]
	));
?>
