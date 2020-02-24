<?php
	include "fctAux.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";
	Twig_Autoloader::register();

	contenu();

	function contenu(){
		$twig = new Twig_Environment(new Twig_Loader_Filesystem("./tpl"));

		$code_erreur = 0;
		$id = "";
		$mdp = "";
		if (isset($_REQUEST["erreur"]))
			$code_erreur = $_GET["erreur"];

		if(isset($_REQUEST["id"]))
			$id = $_REQUEST["id"];

		if(isset($_REQUEST["mdp"]))
			$mdp = $_REQUEST["mdp"];

		$tpl = $twig->loadTemplate("formulaire.tpl");
		echo $tpl->render(array(
			"titre" => "Connexion",
			"css" => array("styleLog"),
			"id" => $id,
			"mdp" => $mdp,
			"code_erreur" => $code_erreur
		));
	}

?>
