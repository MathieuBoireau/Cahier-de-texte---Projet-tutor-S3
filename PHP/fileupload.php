<?php

// Créer le dossier comprenant les pièces jointes s'il n'existe pas.
if (!file_exists('./pj')) {
	mkdir('./pj');
}
uploadFile();

/**
 * Télécharge les fichiers sur le serveur.
 */
function uploadFile() {
	// Dossier où seront stockées les pièces jointes.
	$dossier = './pj/documents_'.date('Y').'-'.date('m').'/';

	for($i = 0; $i < count($_FILES); $i++){
		$doc = array_keys($_FILES)[$i];
		echo $doc;
		$fichier = basename($doc);
		$taille_maxi = 10000000; // 10 Mo
		$taille = filesize($_FILES[$doc]['tmp_name']);

		// Refuser les fichiers trop lourds.
		if($taille > $taille_maxi) {
			$erreur = 'Le fichier doit avoir une taille inférieure à 10 Mo.';
		}

		if(!isset($erreur)) {
			// Remplacer les caractères accentués par leur équivalent sans accent.
			$fichier = strtr($fichier,
				'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
			$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);

			// Créer le dossier des pièces jointes pour le mois courant s'il n'existe pas.
			if (!file_exists($dossier)) {
				mkdir($dossier, 0777);
			}
			else
				chmod($dossier, 0777);

			// Téléchargement du fichier.
			move_uploaded_file($_FILES[$doc]['tmp_name'], $dossier.$fichier);
			chmod($dossier.$fichier, 0777);
			return true;
		}
		return $erreur;
	}
}
?>