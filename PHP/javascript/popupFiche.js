$(function(){

	/**
	 * Fonction valider
	 * Permet de reproduire le fonctionnement d'un formulaire en php
	 */
	$('input[name="valid"]').click(function() {
		window.scrollTo(0, 0); //Remonte en haut de la page
		$("p").each(function(){
			$(this).prop("hidden", true); //Cache tous les messages d'erreurs
		});

		var isOk = true;
		var url = window.location.href.split("%22").join("\""); //Récupère l'url et isole les éléments d'une séance sous la forme d'un tableau
		var seance;
		var evenements = getEvents(); //Récupère les événements affichés
		evenements.forEach((event) =>{
			if(event[1].localeCompare("") == 0){ //Si la description n'est pas remplie, affiche une erreur
				afficherErreur(event[4], "lib");
				isOk = false;
				return;
			}
			if(event[0].localeCompare("Travail à faire") == 0){ //Ces éléments sont vérifiés uniquement si le type d'événement le nécessite
				if(event[2].localeCompare("") == 0){ //Si la date rendu est vide, affiche une erreur
					afficherErreur(event[4], "daterendu")
					isOk=false;
					return;
				}
				if(event[3].localeCompare("") == 0) //Si la durée est vide, la remplace par un null (pour éviter une erreur SQL)
					event[3] = null;
			}
			else{ //La date de rendue et durée n'est pas nécessaire ni utile pour un type d'événement autre que "Travail à faire"
				event[2] = null;
				event[3] = null;
			}
		});
		if(!isOk)
			return; //Arrête de traiter les modifications si des erreurs sont survenues
		var nomFichiers = []; //Tableau reprenant les nom des pièces jointes et leur id dans la base de donnée
		if (url.includes("seance")) {
			seance = getSeanceModif(); //Récupère les éléments modifiés (ou pas) de la séance
			var parametre = url.substr(url.indexOf("[") + 1, url.length - (url.indexOf("[") + 2)).replace(/\"/g, "").split(",");
			//Reprend les éléments initiaux de la séance
			$.ajax({ //Réquête ajax vers ajaxQuery.php en appelant la méthode updateSeance en envoyant les éléments initiaux de la séance, les modifications de la séance et tous les événements
				url: 'ajaxQuery.php',
				type: 'POST',
				data: {
					myFunction: "updateSeance",
					params: {
						initSeance: JSON.stringify(parametre), //Convertion en JSON des tableaux
						seance: JSON.stringify(seance),
						evenements: JSON.stringify(evenements)
					}
				},
				success: function (data) {
					console.log(data);
					if(data.length != 0){ //Si la base de données renvoie des noms de fichier (dans le cas ou des pièces-jointes ont été envoyé)
						nomFichiers = JSON.parse(data);
						exploitationsResultats(evenements, nomFichiers);
					}
				}
			});
			reload(null);//Recharge la page après le traitement des données
		}
		else {
			seance = Array.from(getSeanceAjout());
			if (seance[1].localeCompare("") == 0) {//Si la date de la séance n'est pas renseignée
				afficherErreur("seance", "date");
				return;
			}
			verifSeance(seance, evenements);
		}
	});

	/**
	 * Fonction permettant la suppression d'un événement
	 */
	$('input[name="supprEvt"]').click(function() {
		if(confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')) {//Confirmation de la suppression de l'événement

			idEvent = $(this).attr("id"); //Puisque c'est un serial, l'id de l'évent est unique

			$.ajax({
				url: 'ajaxQuery.php',
				type: 'POST',
				data: {
					myFunction: "supprimerEvt",
					param: {
						idEvent: JSON.stringify(idEvent)
					}
				},
				success: function (data) {
					console.log(data);
					reload(null);
				}
			});
		}
	})

	/**
	 * Fonction permettant de récupérer les éléments de la séance lorsqu'on est en train d'en modifier une
	 * @returns {[module, date, utilisateur, typeSeance, groupe]}
	 */
	function getSeanceModif(){
		var fiche = $("#seance");
		var retour = [];
		var module = fiche.find("tr:eq(0)").find("td:eq(0)").html();
		retour.push(module.substr(module.indexOf(":") + 2));
		var date = fiche.find("tr:eq(0)").find("td:eq(1)").html();
		retour.push(date.substr(date.indexOf(":") + 2, 10));
		retour.push(fiche.find('#user').attr("value"));
		var typeSeance = fiche.find("tr:eq(1)").find("td:eq(0)").html();
		retour.push(typeSeance.substr(typeSeance.indexOf(":") + 2));
		var groupe = fiche.find("tr:eq(1)").find("td:eq(1)").html();
		retour.push(groupe.substr(groupe.indexOf(":") + 2));
		return retour;
	}

	/**
	 * Fonction permettant de récupérer les éléments de la séance lors de sa création
	 * @returns {[module, date, utilisateur, typeSeance, groupe]}
	 */
	function getSeanceAjout(){
		var fiche = $("#seance");
		var retour = [];
		retour.push(fiche.find('select[name="module"]').children("option:selected").val());
		retour.push(fiche.find('input[name="date"]').val());
		retour.push(fiche.find('#user').attr("value"));
		retour.push(fiche.find('select[name="typeSeance"]').children("option:selected").val());
		retour.push(fiche.find('select[name="groupe"]').children("option:selected").val());
		return retour;
	}

	function getEvents(){
		var retour = [];
		$("section").each(function() {//Les événements sont tous dans une section
			var event = [];
			var fiche = $(this);
			var num = fiche.attr("id"); //L'id de l'événement est renseigné dans l'id de la section
			if (num.localeCompare("+")==0 && fiche.find('input[name="lib"]').val().localeCompare("")==0){//Si l'id de l'evenement est + (nouvel événement) et que la description n'est pas remplie
				if(window.location.href.split("%22").join("\"").includes("seance")) //Si la séance est en cours de création
					return false;
			}
			var type = fiche.find('select[name="typeEvent"]').val();
			event.push(type);
			event.push(fiche.find('input[name="lib"]').val());
			var dateRendu = (fiche.find('input[name="daterendu"]').val());
			event.push(dateRendu);
			event.push(fiche.find("input[name='duree']").val());
			event.push(num);
			var documents = [];
			fiche.find("input[type='file']").each(function(){//Pour tous les input de pièces-jointes
				if($(this).val().localeCompare("") != 0){//Si une pièce jointe est renseignée
					documents.push($(this).val().split("\\")[2]);//Récupère le nom de la pièce jointe
				}
			});
			event.push(documents);
			retour.push(event);
		})
		return retour;
	}

	$('input[name="cancel"]').click(function(){
		window.close(); //Ferme la fenêtre si le bouton quitter est préssé
	})

	function afficherErreur(idEvent, element) {
		if(idEvent.localeCompare("seance") == 0) //Récupère le tableau de la séance ou la section de l'événement en fonction de idEvent
			var section = $("#seance");
		else
			var section = $("section[id=\""+ idEvent +"\"]");
		section.find("input[name="+ element +"]").next().removeAttr("hidden"); //Révèle l'erreur lié à l'élément
		if(idEvent.localeCompare("seance") == 0) //Regarde si l'erreur est sur un événement ou la séance puis affiche un message d'erreur
			alert("Entrée erronée sur la séance");
		else
			alert("Entrée erronée sur l'événement " + section.find("tr:eq(0)").find("td:eq(0)").html().substr(section.find("tr:eq(0)").find("td:eq(0)").html().indexOf(":") +1));
	}

	/**
	 * Fonction permettant de téléverser une pièce jointe renseignée
	 * @param idEvent
	 * @param nomFichier
	 * @param nomBase
	 */
	function uploadFile(idEvent, nomFichier, nomBase) {
		var i = 0;//Compteur des pièces-jointes
		var fiche = $("section[id=\"" + idEvent + "\"]");
		fiche.find("input[type='file']").each(function () {
			i++;
			if ($(this).val().includes(nomFichier)) {
				var form_data = new FormData();
				var oFReader = new FileReader();
				var f = document.getElementById("ev" + idEvent + "pj" + i).files[0]; //Récupère le fichier renseignée d'en l'input de la pièce jointe
				oFReader.readAsDataURL(f);
				var fsize = f.size||f.fileSize;
				if(fsize > 2000000) //Vérifie que le fichier n'excède pas 2Mo
				{
					//Si oui, le supprime de la base de donnée et affiche un message d'erreur
					alert("Le fichier " + f.name + " est trop volumineux (Max 2Mo)");
					deletePj(nomBase);
					return;
				}
				form_data.append(nomBase, f); //Enregistre le fichier avec comme nom l'id unique généré par la base de donnée
				$.ajax({ //Requête ajax envoyant le fichier à fileupload.php afin de l'enregistrer sur le pc
					url: "fileupload.php",
					method: "POST",
					data: form_data,
					contentType: false,
					cache: false,
					processData: false,
					success: function(data){
						console.log(data);
					},
				})
			}
		})
	}

	/**
	 * Fonction récupérant les événements et le tableau lien nom de pièce jointe et id unique
	 * Vérifie pour chaque événement s'il possède un pièce-jointe à récupérer
	 * Si oui, appel uploadFile avec en paramètre son id, le nom du fichier et l'id du fichier dans la base de donnée
	 * @param evenements
	 * @param nomFichiers
	 */
	function exploitationsResultats(evenements, nomFichiers){
		for(var i = 0; i < evenements.length; i++){ //Chaque ligne de nomFichiers correspond à un événement, dans l'ordre d'affichage
			evenements[i][5].forEach((doc)=>{
				if(doc in nomFichiers[i]){//nomFichiers[i] est un objet ayant comme clé le nom de la pièce jointe
					uploadFile(evenements[i][4], doc, nomFichiers[i][doc]);
				}
			})
		}
	}

	/**
	 * Fonction gérant la suppression d'une pièce jointe
	 */
	$("img").click(function(){
		if(confirm('Êtes-vous sûr de vouloir supprimer cette pièce-jointe ?')){
			var nomPj = $(this).prev().attr("href");//Récupère l'id unique de la pièce jointe
			nomPj = nomPj.substr(nomPj.indexOf("/") +1);
			nomPj = nomPj.substr(nomPj.indexOf("/") +1);
			deletePj(nomPj);
			reload(null);
		}
	})

	/**
	 * Supprime une pièce jointe à partir de son id unique
	 * @param nomFichier
	 */
	function deletePj(nomFichier){
		$.ajax({
			type: "POST",
			url: "ajaxQuery.php",
			data: {
				myFunction: "deletePj",
				params: {
					nomPj: nomFichier
				}
			},
			success: function(data) {
				console.log(data);
			}
		})
	}

	/**
	 * Fonction vérifiant si une séance existe déjà
	 * Si oui, affiche un message d'erreur
	 * Si non, continue le traitement des informations
	 * @param seance
	 * @param evenement
	 */
	function verifSeance(seance, evenement){
		$.ajax({
			type: "POST",
			url: "ajaxQuery.php",
			data: {
				myFunction: "verifSeance",
				params: {
					seance: JSON.stringify(seance)
				}
			},
			success: function(data){//Le php echo "true" ou "false" en fonction de si la séance existe déjà
				if(data.localeCompare("true") == 0)
					ajoutSeance(seance, evenement);
				else
					alert("La séance existe déjà !")
			},
			fail: function(){
				alert("Erreur inconnue")
			}
		})
	}

	/**
	 * Fonction ajoutant une séance avec ses événements
	 * @param seance
	 * @param evenements
	 */
	function ajoutSeance(seance, evenements){
		//Change l'url pour y ajouter les éléments de la séance créé (pour le rechargement de la page)
		$.ajax({
			url: 'ajaxQuery.php',
			type: 'POST',
			data: {
				myFunction: "addSeance",
				params: {
					seance: JSON.stringify(seance),
					evenement: JSON.stringify(evenements)
				}
			},
			success: function (data) {
				console.log(data);
				if(data.length != 0){ //Si des noms de fichier sont envoyé depuis le ajax, les récupère dans nomFichiers
					nomFichiers = JSON.parse(data);
					exploitationsResultats(evenements, nomFichiers);
				}
			}
		});
		reload(seance);
	}

	/**
	 * Rechargement de la popup ainsi que la page journalDeBord pour actualiser l'affichage des séances
	 */
	function reload(seance){
		setTimeout(function(){
			window.opener.location.reload(true);
			if(Array.isArray(seance))
				window.location.href = window.location.href + "?seance=[\""+seance[0]+"\",\"" + seance[1] + "\",\"" + seance[2] +"\",\"" + seance[3] +"\",\"" + seance[4] + "\"]";
			else
				window.location.reload(true);
		}, 200); //Le timer est nécessaire pour éviter une erreur avec le ajax (asynchrone)
	}
});
