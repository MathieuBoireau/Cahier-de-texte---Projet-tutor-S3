<?php

	include 'fctAux.inc.php';
	require_once "DB.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";
	Twig_Autoloader::register();

	$utilisateur = getStartAdmin();
	contenu();

	function contenu() {
		// Si un bouton a été cliqué, rediriger au bon endroit.
		if(isset($_REQUEST['choix'])) {
			$choix = $_GET['choix'];
			switch($choix) {
				case "utilisateur":
					header("location: modificationsUtilisateurs.php");
					break;
				case "module":
					header("location: modificationsModules.php");
					break;
				case "seance":
					header("location: modificationsSeancesEvenements.php");
					break;
				case "groupe":
					header("location: modificationsGroupes.php");
					break;
				default:
					header("Location: menuAdmin.php");
			}
		}
		else
			afficherChoix();
	}

	/**
	 * Affichage de la page.
	 */
	function afficherChoix() {
		global $utilisateur;
		$twig = new Twig_Environment(new Twig_Loader_Filesystem("./tpl"));
		$tpl = $twig->loadTemplate("menuAdmin.tpl");
		echo $tpl->render(array(
			"titre" =>"Menu Administrateur",
			"css" => array("stylePageAdmin"),
			"user" => $utilisateur
		));
	}
 ?>
