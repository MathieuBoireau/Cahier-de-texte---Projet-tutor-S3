<?php
	include 'fctAux.inc.php';
	require_once "DB.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";

	Twig_Autoloader::register();
	$twig = new Twig_Environment( new Twig_Loader_Filesystem("./tpl"));

	$utilisateur = getStartAdmin();
	$db = DB::getInstance();

	// Modification en base de données si le formulaire a été rempli.
	if(isset($_REQUEST["listetuteur"]) && isset($_REQUEST["listegroupe"])) {
		try {
			$db->affecterTuteurGroupe($_REQUEST["listetuteur"], $_REQUEST["listegroupe"]);
		} catch (PDOException $e) {
			$erreur = "L'affectation du tuteur à ce groupe existe déjà.";
		}
	}

	if(isset($_REQUEST["listetuteursup"]) && isset($_REQUEST["listegroupesup"])) {
		$db->supprimerTuteurGroupe($_REQUEST["listetuteursup"], $_REQUEST["listegroupesup"]);
	}

	// Affichage de la page.
	$tuplesGroupe  = $db->getGroupes();
	$tuplesTuteurs = $db->getTuteurs();
	$tuplesGroupesTuteurs = $db->getTuteursGroupes();
	$tpl = $twig->loadTemplate( "modificationsGroupes.tpl" );
	echo $tpl->render(array(
		"titre"  => "Modification Groupes",
		"css" => array("styleModificationsGroupes"),
		"tuplesGroupe" => $tuplesGroupe,
		"tuplesTuteurs"=> $tuplesTuteurs,
		"tuplesGroupesTuteurs" => $tuplesGroupesTuteurs
	));
?>
