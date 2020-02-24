<?php
	include_once 'fctAux.inc.php';
	require_once "DB.inc.php";
	require_once "Twig/lib/Twig/Autoloader.php";

	Twig_Autoloader::register();
	$twig = new Twig_Environment( new Twig_Loader_Filesystem("./tpl"));
	$tpl = $twig->loadTemplate( "etatSeances.tpl" );

	$utilisateur = getStart();
	$db = DB::getInstance();
	$tuplesModule = $db->getModules();
	$contraintes = $db->getContraintes();
	$groupes = $db->getGroupes();
	$utilisateurs = $db->getUtilisateurs();
	$idUser = $utilisateur->getId();

	//semaphore
	$tuplesSemaphore = $db->getSemaphore($idUser);

	// Pour un affichage mois par mois
	$semaineDebut = date("Y-m-d", strtotime(date("Y")."-".date("m")."-01"));
	$semaineFin = date("Y-m-d", strtotime(date("Y")."-".date("m")."-".date("t")));

	$moduleCourant = "default";
	$dateSeanceDebut = $semaineDebut;
	$dateSeanceFin = $semaineFin;
	$typeSeanceCourant = "default";
	$groupeCourant = "default";
	$proprietaireCourant = "default";
	$typeEventCourant = "default";
	$dateEventDebut = "";
	$dateEventFin =  "";
	$messageErreur = "";

	$couleurBoutonEtat1 = $db->getCoulBoutEtat1()[0];
	$couleurBoutonEtat2 = $db->getCoulBoutEtat2()[0];
	$couleurTexteEtat1  = $db->getCoulTxtEtat1()[0];
	$couleurTexteEtat2  = $db->getCoulTxtEtat2()[0];


	if(isset($_REQUEST["selectModule"]))     { $moduleCourant = $_GET["selectModule"]; }
	if(isset($_REQUEST["dateDebS"]))         { $dateSeanceDebut = $_GET["dateDebS"]; }
	if(isset($_REQUEST["dateFinS"]))         { $dateSeanceFin = $_GET["dateFinS"]; }
	if(isset($_REQUEST["selectTypeSeance"])) { $typeSeanceCourant = $_GET["selectTypeSeance"]; }
	if(isset($_REQUEST["selectGroupe"]))     { $groupeCourant = $_GET["selectGroupe"]; }
	if(isset($_REQUEST["selectProprio"]))    { $proprietaireCourant = $_GET["selectProprio"]; }
	if(isset($_REQUEST["selectTypeEvent"]))  { $typeEventCourant = $_GET["selectTypeEvent"]; }
	if(isset($_REQUEST["dateDebE"]))         { $dateEventDebut = $_GET["dateDebE"]; }
	if(isset($_REQUEST["dateFinE"]))         { $dateEventFin = $_GET["dateFinE"]; }


	$seances = $db->getSeancesSemaine($dateSeanceDebut, $dateSeanceFin, $moduleCourant, $typeSeanceCourant, $groupeCourant, $proprietaireCourant);


	// Si l'utilisateur est un tuteur, on limite l'affichage aux groupes qu'il tuteure, ainsi
	// qu'à l'année scolaire en cours
	if($utilisateur->getRole() == "T") {
		// Limitation dans le temps
		$anneeSuivante = intval($utilisateur->getAnneeCreation())+1;
		$datePasseeLimite = date("Y-m-d", strtotime("{$utilisateur->getAnneeCreation()}-09-01"));
		$dateFutureLimite = date("Y-m-d", strtotime("{$anneeSuivante}-06-30"));

		if(strtotime($dateSeanceDebut) < strtotime($datePasseeLimite))
			$dateSeanceDebut = $datePasseeLimite;

		if(strtotime($dateFutureLimite) < strtotime($dateSeanceFin))
			$dateSeanceFin = $dateFutureLimite;


		// Nouvelle récupération des séances avec les nouvelles dates limitées
		$seances = $db->getSeancesSemaine($dateSeanceDebut, $dateSeanceFin, $moduleCourant, $typeSeanceCourant, $groupeCourant, $proprietaireCourant);


		// Limitation des groupes
		$tuplesGroupe = $utilisateur->getGroupes();
		foreach($tuplesGroupe as $groupe) {
			$accesGroupes[] = $groupe->getGroupe();
		}

		// Variable nécessaire car le for suivant modifie le count($seance) pendant la boucle, réduisant
		// son nombre durant le process. Cela entraine une réduction du nombre de tours de boucle égal
		// au nombre de séances retirées dans cette boucle
		$nbSeances = count($seances);
		for($i = 0; $i < $nbSeances; $i++) {
			if(!in_array($seances[$i]->getGroupe(), $accesGroupes)) {
				unset($seances[$i]);
			}
		}
	}


	//Récupération des events concernants les séances récupérées ci-dessus
	$events = array();
	foreach ($seances as $seance) {
		foreach ($db->getEvents($seance) as $key) {
			$events[] = $key;
		}
	}

	// Rédaction du message lorsque la quantité de séances recherchées dépasse la limite autorisée
	if(count($seances) > $db->getMaxSeancesAffichables())
		$messageErreur = "Votre recherche dépasse le nombre maximum de séances affichables (".$db->getMaxSeancesAffichables()."), veuillez réitérer votre recherche en affinant les filtres";

	echo $tpl->render(array(
		"titre"  =>"Etat Séance",
		"css" => array("styleEtatSeances"),
		"user" => $utilisateur,
		"tuplesModule" => $tuplesModule,
		"semaineDebut" => $semaineDebut,
		"semaineFin" => $semaineFin,
		"contraintes" => $contraintes,
		"groupes" => $groupes,
		"utilisateurs" => $utilisateurs,
		"moduleCourant" => $moduleCourant,
		"dateSeanceDebut" => $dateSeanceDebut,
		"dateSeanceFin" =>$dateSeanceFin,
		"typeSeanceCourant" => $typeSeanceCourant,
		"groupeCourant" => $groupeCourant,
		"proprietaireCourant" =>$proprietaireCourant,
		"dateEventDebut" => $dateEventDebut,
		"dateEventFin" => $dateEventFin,
		"typeEventCourant" => $typeEventCourant,
		"seances" => $seances,
		"events" =>$events,
		"idUser" => $idUser,
		"tuplesSemaphore" => $tuplesSemaphore,
		"couleurBoutonEtat1" => $couleurBoutonEtat1[0],
		"couleurBoutonEtat2" => $couleurBoutonEtat2[0],
		"couleurTexteEtat1"  => $couleurTexteEtat1[0],
		"couleurTexteEtat2"  => $couleurTexteEtat2[0],
		"message" => $messageErreur
	));
?>
