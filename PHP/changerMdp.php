<?php
	include_once "fctAux.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";
	require_once "DB.inc.php";
	Twig_Autoloader::register();

	// Commencer la session pour changer le mot de passe.
	session_start();
	if (!isset($_SESSION['nouveau'])) {
		header('Location: login.php');
	} else {
		main();
	}

	function main(){
		$id_util = $_SESSION['nouveau'];

		// Affichage du formulaire
		$affichErr = true;
		$mdp = $confirmMdp = "";
		if(isset($_POST['mdp']))
			$mdp = $_POST["mdp"];
		else
			$affichErr = false;
		
		if(isset($_POST['confirmMdp']))
			$confirmMdp = $_POST["confirmMdp"];
		else
			$affichErr = false;
			
		$code_erreur = 0;
		if(!estMdpValide($mdp))
			$code_erreur = 1;
		else if($mdp != $confirmMdp)
			$code_erreur = 2;

		if($code_erreur){ // Formulaire incorrect
			if($affichErr)
				form($code_erreur);
			else
				form(0);
		} else {
			// On change le mot de passe.
			$db = DB::getInstance();
			$db->setMdpUtilisateur($id_util, $mdp, "");
			$utilisateur = $db->connexion($id_util, $mdp)[0];
			unset($_SESSION['nouveau']);
			session_start();
			$_SESSION['utilisateur'] = serialize($utilisateur);
			header("Location: accueil.php");
		}
	}

	/**
	 * Affichage du formulaire.
	 * @param mixed $code_erreur Le code d'erreur.
	 */
	function form($code_erreur){
		$twig = new Twig_Environment(new Twig_Loader_Filesystem("./tpl"));

		$tpl = $twig->loadTemplate("changeMdp.tpl");

		echo $tpl->render(array(
			"titre" => "Changement de mot de passe",
			"css" => array("styleLog"),
			"code_erreur" => $code_erreur
		));

	}
?>