<?php

	include 'fctAux.inc.php';
	require_once "DB.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";
	Twig_Autoloader::register();

	$utilisateur =  getStartAdmin();

	contenu();

	function contenu() {
		$twig = new Twig_Environment(new Twig_Loader_Filesystem("./tpl"));
		$tpl = $twig->loadTemplate("modificationsUtilisateurs.tpl");

		global $utilisateur;
		$currentId = $utilisateur->getId();
		$editMod = null;
		$utilisateurId = null;
		$nom = null;
		$prenom = null;
		$role_utilisateur = null;

		if (isset($_REQUEST["utilisateurId"]))    $utilisateurId = $_GET["utilisateurId"];
		if (isset($_REQUEST["nom"]))              $nom = $_GET["nom"];
		if (isset($_REQUEST["prenom"]))           $prenom = $_GET["prenom"];
		if (isset($_REQUEST["role_utilisateur"])) $role_utilisateur = $_GET["role_utilisateur"];

		$db = DB::getInstance();
		$tuplesUtilisateurs = $db->getUtilisateurs();

		if(isset($_REQUEST["edit"])) {
			$editMod = $_GET["edit"];

			// Supprimer un utilisateur (l'administrateur ne peut pas se supprimer lui-même)
			if($editMod == "supprimer" && $utilisateurId != null && $utilisateurId != $currentId) {
				$resultat = $db->supprimerUtilisateur($utilisateurId);
				header("Location: modificationsUtilisateurs.php");
			}

			if($editMod == "nouvMdp" && $utilisateurId != null) {
				$nouvMdp = creerMdp();
				$resultat = $db->setMdpUtilisateur($utilisateurId, $nouvMdp, $nouvMdp);
				header("Location: modificationsUtilisateurs.php");
			}
		}

		// Modification d'un utilisateur
		if(isset($_REQUEST["modification"])) {
			// Un admin ne peut pas modifier son propre rôle
			if ($utilisateurId == $currentId && $role_utilisateur != $utilisateur->getRole()) {
				header("Location: modificationsUtilisateurs.php");
				die();
			}
			if (paramNonNull($utilisateurId, $nom, $prenom, $role_utilisateur)) {
				$db->modifierUtilisateur($utilisateurId, $nom, $prenom, $role_utilisateur);
			}
			header("Location: modificationsUtilisateurs.php");
		}

		// Création d'un nouvel utilisateur
		$erreur = "";
		if(isset($_REQUEST["creation"])) {
			if(paramNonNull($utilisateurId, $nom, $prenom, $role_utilisateur)) {
				try{
					$db->ajouterUtilisateur($utilisateurId, $nom, $prenom, creerMdp(), $role_utilisateur);
				}catch(PDOException $e){
					$erreur = "Une erreur est survenue lors de l'ajout de l'utilisateur.";
				}
			}
			if(!$erreur) // On affiche la page avec l'utilisateur créé
				header("Location: modificationsUtilisateurs.php");
		}

		echo $tpl->render(array(
			"titre" => "Modification Utilisateurs",
			"css" => array("styleModificationsUtilisateurs"),
			"tuplesUtilisateurs" => $tuplesUtilisateurs,
			"editMod" => $editMod,
			"utilisateurId" => $utilisateurId,
			"erreur" => $erreur
		));

	}

	/**
	 * Crée un mot de passe aléatoire.
	 * Utilisé pour la génération d'un mot de passe.
	 * @return string Le mot de passe aléatoire.
	 */
	function creerMdp() {
		$randMdp = substr(md5(uniqid()), 0, rand(8, 12));

		for ($i = 0; $i < strlen($randMdp); $i++) {
			if (random_int(0, 10) <= 7) {
				// Changer aléatoirement les minuscules en majuscules.
				if (ctype_lower($randMdp[$i])) {
					$randMdp[$i] = strtoupper($randMdp[$i]);
				}
			}

			if (random_int(0, 10) <= 8) {
				// Changer les lettres pour qu'elles comprennent tout l'alphabet.
				if (ctype_alpha($randMdp[$i])) {
					$randMdp[$i] = chr(ord($randMdp[$i]) + random_int(1, 20));
				}
			}

			// Ne pas utiliser de 0 ou 1 dans les mots de passe,
			// pour éviter la confusion avec O et I.
			if (preg_match("/[01]/", $randMdp[$i])) {
				// remplacer 0 ou 1 par une lettre minuscule.
				$randMdp[$i] = chr(random_int(97, 122));
			}
		}

		return $randMdp;
	}
 ?>
