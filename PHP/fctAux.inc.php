<?php
	include_once './Beans/utilisateur.inc.php';

	/**
	 * Obtient le nom complet en français d'un mois à partir de son indice.
	 * Les indices vont de 0 à 11, les mois vont de janvier à décembre.
	 * @param int $ind L'indice du mois à obtenir.
	 * @return string Le nom complet du mois.
	 */
	function getNomMois($ind){
		$mois = array(
			"Janvier","Février","Mars","Avril","Mai","Juin","Juillet",
			"Août","Septembre","Octobre","Novembre","Décembre"
		);

		return $mois[$ind];
	}

	/**
	 * Commence une session pour l'administrateur.
	 * @return Utilisateur l'utilisateur connecté.
	 */
	function getStartAdmin() {
		session_start();
		$utilisateur = unserialize($_SESSION['utilisateur']);

		if(!isset($_SESSION['utilisateur']))
			header("Location: login.php");
		if($utilisateur->getRole()!="A" && $utilisateur->getRole()!="AE")
			header("Location: login.php");
		return $utilisateur;
	}

	/**
	 * Commence une session pour l'utilisateur.
	 * @return Utilisateur l'utilisateur connecté.
	 */
	function getStart() {
		session_start();
		$utilisateur = unserialize($_SESSION['utilisateur']);

		if(!isset($_SESSION['utilisateur']))
			header("Location: login.php");
		if($utilisateur->getRole()== "A")
			header("Location: login.php");
		return $utilisateur;
	}

	/**
	 * Vérifie que tous les paramètres ne sont pas null ou vides.
	 * @param mixed ...$param Les paramètres à tester.
	 * @return bool true si aucun paramètre n'est null, false sinon.
	 */
	function paramNonNull(...$param) {
		return !in_array(null, $param) || !in_array("", $param);
	}

	/**
	 * Vérifie que le mot de passe est valide.
	 * Un mot de passe est considéré valide si :
	 * - il contient au moins 2 majuscules,
	 * - il contient au moins 2 minuscules,
	 * - il contient au moins 2 caractères autres que des lettres,
	 * - il contient au moins 8 caractères.
	 * @param string $mdp Le mot de passe à tester.
	 * @return bool true si le mot de passe est valide, false sinon.
	 */
	function estMdpValide($mdp){
		$mdp = iconv("UTF-8","ISO-8859-1//IGNORE",$mdp);
		$nbMaj = $nbMin = $nbSpe = 0;
		for($i=0; $i<strlen($mdp); $i++){
			$char = substr($mdp,$i,1);
			if(ctype_upper($char))
				$nbMaj++;
			if(ctype_lower($char))
				$nbMin++;
			if(!ctype_alpha($char))
				$nbSpe++;
		}
		return strlen($mdp)>=8 && $nbMaj>=2 && $nbMin>=2 && $nbSpe>=2;
	}
 ?>
