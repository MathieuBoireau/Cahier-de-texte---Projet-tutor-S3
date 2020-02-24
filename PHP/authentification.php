<?php
	include "fctAux.inc.php";
	require_once "DB.inc.php";

	// Constantes correspondant à des flags d'erreur.
	const ERROR_ID = 0x01;
	const ERROR_MDP = 0x02;
	const ERROR_LOGIN = 0x04;

	verifLogin();

	function verifLogin() {
		$code_erreur = 0;

		// Activer les flags quand le champ du formulaire n'est pas rempli.
		if(empty($_POST["id"]))
			$code_erreur |= ERROR_ID;
		if(empty($_POST["mdp"]))
			$code_erreur |= ERROR_MDP;

		// Si un ou plusieurs champs ne sont pas remplis, afficher les erreurs correspondantes.
		if($code_erreur) {
			header("Location: login.php?erreur=$code_erreur");
		} else {
			$id = $_POST["id"];
			$mdp = $_POST["mdp"];
			$db = DB::getInstance();

			// connexion renvoie un tableau avec un unique tuple utilisateur
			// si l'authentification a réussi, et un tableau vide sinon.
			$utilisateur = $db->connexion($id, $mdp);

			if($utilisateur[0]->getId() != null) {
				session_start();

				// L'utilisateur doit-il changer son mot de passe ?
				if($utilisateur[0]->getMdpGenere()!=null){
					// Commencer une session "temporaire"
					$_SESSION['nouveau'] = $utilisateur[0]->getId();
					header("Location: changerMdp.php");
					return;
				}

				$_SESSION['utilisateur'] = serialize($utilisateur[0]);
				header("Location: accueil.php");
			}
			else
				header("Location: login.php?erreur=".ERROR_LOGIN);
		}
	}
 ?>
