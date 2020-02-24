$(function(){
	/**
	 * Fonction supprimant une séance
	 */
	$('img[src="./images/poubelle.png"]').click(function(){
		if(confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')){
			//Récupération des éléments clés de la séance
			var fiche = $(this).closest("table");
			var user = fiche.find("tr:eq(1)").find("td:eq(1)").attr("id");
			var module = fiche.find("tr:eq(1)").find("td:eq(0)").html();
			var date = fiche.find("tr:eq(1)").find("td:eq(2)").attr("id");
			var type = fiche.find("tr:eq(1)").find("td:eq(3)").html();
			var groupe = fiche.find("tr:eq(1)").find("td:eq(4)").html();
			$.ajax({
				type: "POST",
				url: "ajaxQuery.php",
				data: {
					myFunction: "deleteSeance",
					params: {
						user: user,
						suppr: module.substr(module.indexOf(":") + 2) + "|" + date + "|" + user + "|" + type.substr(type.indexOf(":") + 2) + "|" + groupe.substr(groupe.indexOf(":") + 2)
					}
				},
				success: function(data) {
					console.log(data);
				}
			})
			setTimeout(function(){window.location.reload(true);}, 500);//Recharge la page après 500ms (la fonction peut ne pas marcher sans ce timer)
		}
	});

	/**
	 * Fonction ouvrant une popup de modification de séance
	 */
	$('img[src="./images/crayon.png"]').click(function(){
		//Définit la taille de la popup
		var top = screen.width / 50;
		var left = (screen.height / 3);
		//Récupèration des données de la séance
		var fiche = $(this).closest('table');
		var module = fiche.find("tr:eq(1)").find("td:eq(0)").html();
		var date = fiche.find("tr:eq(1)").find("td:eq(2)").attr("id");
		var type = fiche.find("tr:eq(1)").find("td:eq(3)").html();
		var groupe = fiche.find("tr:eq(1)").find("td:eq(4)").html();

		var seance = [];
		seance.push(module.substr(module.indexOf(":") + 2));
		seance.push(date);
		seance.push(fiche.find("tr:eq(1)").find("td:eq(1)").attr("id"));
		seance.push(type.substr(type.indexOf(":") + 2));
		seance.push(groupe.substr(groupe.indexOf(":") + 2));
		var windows = window.open('popupFiche.php?seance='+JSON.stringify(seance),'Édition de séance','menubar=no, width=1300, height=800, top=' + top + ', left=' + left);//Ouvre une popup avec les donneés de la séance dans l'url
	});

	/**
	 * Fonction ouvrant une popup pour ajouter une séance
	 */
	$('#modif2').click(function(){
		var top = screen.width / 50;
		var left = (screen.height / 3);
		var win = window.open('popupFiche.php','Ajout de séance','menubar=no, width=1300, height=800, top=' + top + ', left=' + left);
	})
});