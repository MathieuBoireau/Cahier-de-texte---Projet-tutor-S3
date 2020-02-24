<?php
	include "fctAux.inc.php";

	/**
	 * Script de redirection vers la page d'accueil
	 * en fonction du rôle de l'utilisateur connecté.
	 */

	session_start();
	$utilisateur = unserialize($_SESSION['utilisateur']);

	if(!isset($_SESSION['utilisateur']))
		header("Location: login.php");
	else
		contenu($utilisateur);

	function contenu($utilisateur) {
		switch ($utilisateur->getRole()) {
			case "A":
				header('Location: menuAdmin.php');
				break;
			case "AE":
			case "E":
			case "T":
				header('Location: journalDeBord.php');
				break;
		}
	}
?>