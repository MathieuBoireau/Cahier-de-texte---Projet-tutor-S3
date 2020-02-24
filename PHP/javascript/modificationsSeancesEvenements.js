function changerAttribut() {
	var liste2 = document.getElementsByClassName("sup");
	for (var i=0; i<liste2.length; i++) {
		if (liste2[i].style.visibility=="hidden"){
			liste2[i].style.visibility="initial";
			document.getElementById("modif01").value = "Terminer";
		}
		else {
			liste2[i].style.visibility="hidden";
			document.getElementById("modif01").value = "Modifier";
		}
	}

	/*var liste3 = document.getElementsByClassName("selectTypeSeance");
	for (var i=0; i<liste3.length; i++) {
		if (liste3[i].disabled){
			liste3[i].removeAttribute('disabled');
		}
		else {
			liste3[i].setAttribute('disabled','');
		}
	}*/

	var liste = document.getElementsByClassName("boutonAjoutNouvelleSeance");
	for (var i=0; i<liste.length; i++) {
		if (liste[i].style.visibility=="hidden"){
			liste[i].style.visibility="initial";
		}
		else {
			liste[i].style.visibility="hidden";
		}
	}
	var liste4 = document.getElementsByClassName("nouvelleSeance");
	for (var i=0; i<liste4.length; i++) {
		if (liste4[i].style.visibility=="hidden"){
			liste4[i].style.visibility="initial";
		}
		else {
			liste4[i].style.visibility="hidden";
		}
	}

}

function changerCouleurSemaphore() {
	var semaphores = [];
	var texte = [];
	texte.push("texte");
	texte.push(document.getElementById("couleurEtat1Texte").value);
	texte.push(document.getElementById("couleurEtat2").value);
	var vu = [];
	vu.push("vu");
	vu.push(document.getElementById("couleurEtat1").value);
	vu.push(document.getElementById("couleurEtat2").value);
	semaphores.push(texte);
	semaphores.push(vu);
	if (confirm('Êtes-vous sûr de vouloir modifier les couleurs ?')) {
		$.ajax({
				url : 'ajaxQuery.php',
				type : 'POST',
				data :{
					myFunction : "changerCouleurSemaphores",
					params : {
						semaphores: JSON.stringify(semaphores)
					}
				},
				success: function(data) {
					document.location.reload();
				}
		})
	}
}

function supprTypeSeances() {
	type = document.getElementsByClassName("selectTypeSeance")[0].value;
	if (confirm('Êtes-vous sûr de vouloir supprimer le type de séances ' + type + ' ?')) {
		$.ajax({
			url: 'ajaxQuery.php',
			type: 'POST',
			data: {
				myFunction: "supprTypeSeance",
				param: {
					type: JSON.stringify(type)
				}
			},
			success: function (data) {
				document.location.reload();
			}
		})
	}
}

function ajoutTypeSeances() {
	type = document.getElementsByClassName("nouvelleSeance")[0].value;
	$.ajax({
		url: 'ajaxQuery.php',
		type: 'POST',
		data: {
			myFunction: "ajoutTypeSeance",
			param: {
				type: JSON.stringify(type)
			}
		},
		success: function (data) {
			document.location.reload();
		}
	})
}

function modifierTypeSeance() {
	var liste = document.getElementById("nouvelleValeurTypeSeance");
	var valide = document.getElementById("validerModification");
	if (liste.style.visibility=="hidden"){
		liste.style.visibility="initial";
		valide.style.visibility="initial";
	}
	else {
		liste.style.visibility="hidden";
		valide.style.visibility="hidden";
	}
}

function changerAttribut2() {
	var liste2 = document.getElementsByClassName("sup2");
	for (var i=0; i<liste2.length; i++) {
		if (liste2[i].style.visibility=="hidden"){
			liste2[i].style.visibility="initial";
			document.getElementById("modif02").value = "Terminer";
		}
		else {
			liste2[i].style.visibility="hidden";
			document.getElementById("modif02").value = "Modifier";
		}
	}

	/*var liste3 = document.getElementsByClassName("selectTypeEvt");
	for (var i=0; i<liste3.length; i++) {
		if (liste3[i].disabled){
			liste3[i].removeAttribute('disabled');
		}
		else {
			liste3[i].setAttribute('disabled','');
		}
	}*/

	var liste = document.getElementsByClassName("boutonAjoutNouvelleSeance2");
	for (var i=0; i<liste.length; i++) {
		if (liste[i].style.visibility=="hidden"){
			liste[i].style.visibility="initial";
		}
		else {
			liste[i].style.visibility="hidden";
		}
	}
	var liste4 = document.getElementsByClassName("nouvelleSeance2");
	for (var i=0; i<liste4.length; i++) {
		if (liste4[i].style.visibility=="hidden"){
			liste4[i].style.visibility="initial";
		}
		else {
			liste4[i].style.visibility="hidden";
		}
	}

}

function supprimerTypeEvenement() {
	type = document.getElementsByClassName("selectTypeEvt")[0].value;
	if (confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')) {
		$.ajax({
			url : 'ajaxQuery.php',
			type : 'POST',
			data :{
				myFunction : "supprTypeEvt",
				param : {
					type : JSON.stringify(type)
				}
			},
			success: function(data) {
				document.location.reload();
			}
		})
	}
}

function modifierTypeSeanceValide() {
	oldvalue = document.getElementsByClassName("selectTypeSeance")[0].value;
	newvalue = document.getElementById("nouvelleValeurTypeSeance").value;

	if (confirm('Êtes-vous sûr de vouloir modifier cette ligne ?')) {
		$.ajax({
			url : 'ajaxQuery.php',
			type : 'POST',
			data :{
				myFunction : "modifierTypeSeance",
				param : {
					old : JSON.stringify(oldvalue),
					new : JSON.stringify(newvalue)
				}
			},
			success: function(data) {
				document.location.reload();
			}
		})
	}
}

function validerModif2() {
	oldvalue = document.getElementsByClassName("selectTypeEvt")[0].value;
	newvalue = document.getElementById("nouvelleValeurTypeEvenement").value;

	if (confirm('Êtes-vous sûr de vouloir modifier cette ligne ?')) {
		$.ajax({
			url : 'ajaxQuery.php',
			type : 'POST',
			data :{
				myFunction : "modifierTypeEvt",
				param : {
					old : JSON.stringify(oldvalue),
					new : JSON.stringify(newvalue)
				}
			},
			success: function(data) {
				document.location.reload();
			}
		})
	}
}

function ajouterTypeEvenement() {
	type = document.getElementsByClassName("nouvelleSeance2")[0].value;
	$.ajax({
		url : 'ajaxQuery.php',
		type : 'POST',
		data :{
			myFunction : "ajoutTypeEvt",
			param : {
				type : JSON.stringify(type)
			}
		},
		success: function(data) {
			document.location.reload();
		}
	})
}

function modifierTypeEvenement() {
	var liste = document.getElementById("nouvelleValeurTypeEvenement");
	var valide = document.getElementById("validerModification2");
	if (liste.style.visibility=="hidden"){
		liste.style.visibility="initial";
		valide.style.visibility="initial";
	}
	else {
		liste.style.visibility="hidden";
		valide.style.visibility="hidden";
	}
}

