<?php
	require_once 'DB.inc.php';
	$db = DB::getInstance();

	/**
	 * Crée et/ou initialise des mots de passe,
	 * uniquement quand il n'y a pas d'utilisateur.
	 */
	if (!$db->getUtilisateurs()) {
		$db->ajouterUtilisateur('admin', 'admin', 'admin', 'admin', 'A');
		$db->setMdpUtilisateur('admin', 'admin', 'admin');
	}
	
	// Pour tester
	$db->setMdpUtilisateur('admin', 'admin', 'admin');
	$db->setMdpUtilisateur('lepiverp', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('boukachh', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('dufloh', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('nivetl', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('legrixb', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('ba18xxxx', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('lr18xxxx', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('rs18xxxx', 'xxxxxxxx', 'xxxxxxxx');
	$db->setMdpUtilisateur('ft18xxxx', 'xxxxxxxx', 'xxxxxxxx');

	header('Location: login.php');
?>