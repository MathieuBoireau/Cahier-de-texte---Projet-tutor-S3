<?php
	require_once "Twig/lib/Twig/Autoloader.php";
	require 'DB.inc.php';
	include_once 'fctAux.inc.php';

	Twig_Autoloader::register();
	$twig = new Twig_Environment( new Twig_Loader_Filesystem("./tpl"));
	$tpl = $twig->loadTemplate( "popupFicheTemplate.tpl" );

	$user = getStart();

	$db = DB::getInstance();

	$groupes = $user->getGroupes();
	$modules = $user->getModules();
	$date = date("Y-m-d");
	//Tableau reprennant les paramètres tels que le nom du css, tous les groupes, modules, contraintes et l'utilisateur
	$params = array(
		"css" => array("stylePopUp"),
		"modules" => $modules,
		"groupes" => $groupes,
		"contraintes" => $db->getContraintes(),
		"user" => $user,
		"date" => $date,
		"enCrea" => true
	);

	//Si une séance à été renseignée par requête get
	if(isset($_REQUEST["seance"])){
		//Récupère les données en json
		$seanceModif = json_decode($_REQUEST["seance"]);
		//Récupère la séance dans la base de donnée sous forme d'objet Seance
		$seance = $db->getSeance($seanceModif[0],$seanceModif[1],$seanceModif[2],$seanceModif[3],$seanceModif[4]);
		if(!array_key_exists(0, $seance)){
			echo "<script type='text/javascript'>alert('Erreur : seance introuvable');window.opener.location.reload(true);window.close();</script>";
		}
		$seance = $seance[0];
		//L'inclus dans les paramètres, remplace les modules et groupes par ceux de la séance et désactive le mode enCrea
		$params["seance"] = $seance;
		$params["modules"] = $db->getUtilisateur($seance->getIdUser())[0]->getModules();
		$params["groupes"] = $db->getUtilisateur($seance->getIdUser())[0]->getGroupes();
		$params["enCrea"] = false;
	}

	echo $tpl->render($params);
?>
