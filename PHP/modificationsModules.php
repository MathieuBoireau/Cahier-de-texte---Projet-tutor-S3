<?php
	include 'fctAux.inc.php';

	require_once "DB.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";

	Twig_Autoloader::register();
	$twig = new Twig_Environment( new Twig_Loader_Filesystem("./tpl"));

	$utilisateur = getStartAdmin();
	$db = DB::getInstance();

	$tuplesModules = $db->getModules();
	$enseignants = $db->getEnseignantsTuteurs();

	$tpl = $twig->loadTemplate( "modificationsModules.tpl" );

	echo $tpl->render(array(
		"titre"  => "Modification Modules",
		"css" => array("styleModificationsModules"),
		"tuplesModules" => $tuplesModules,
		"enseignants" => $enseignants
	));

?>
