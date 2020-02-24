{% include "enTete.tpl" %}
<script src="javascript/jquery.js"></script>

<header class="cadre">
	<h1>Modification des groupes</h1>
</header>

	<a href="menuAdmin.php" class="home">
	<div class="container">
		<img src="images/home.png" alt="home">
		<div class="middle">
			<div class="texteMilieu">Retour au menu</div>
		</div>
	</div>
	</a>

	<main>
		<section class="sectionGauche">
			<h2 class="cadre">Modifer les groupes</h2>
			<table class="cadre tableauAffichageGroupe">
				<tr>
					<th>Groupe</th>
					<th>Groupe père</th>
					<th><input type="button" class="option sup" value="Annuler"  onclick="document.location.reload()" style="visibility:hidden" ></th>
					<th><input type="button" class="option" id="modif01" value="Modifier" onclick="changerAttribut()"></th>
				</tr>

{% for groupe in tuplesGroupe %}
				<tr>
					<td>
						<input type="text" value="{{groupe.getGroupe()}}" name="" id="modif" class="modif" readonly>
					</td>
					<td>
						<select name="groupePere" class="gp" disabled>
							<option value="none"></option>
{% for groupe2 in tuplesGroupe %}
{% if groupe.getGroupePere() == groupe2.getGroupe() %}
							<option selected value="{{groupe.getGroupePere()}}">{{groupe.getGroupePere()}}</option>
{% else %}
							<option value="{{groupe2.getGroupe()}}">{{groupe2.getGroupe()}}</option>
{% endif %}
{% endfor %}
						</select>
					</td>
					<td colspan="2">
						<input type="button" value="Supprimer" name="sup" class="option" onclick="supprimerGroupe('{{groupe.getGroupe()}}')" style="width: 100%;">
					</td>
				</tr>
{% endfor %}
			</table>
		</section>

		<section class="sectionCreationGroupe">
			<h2 class="cadre">Créer un groupe</h2>
			<table class="cadre tableauCreationGroupe">
				<tr>
					<td>Libellé :</td>
					<td><input type="text" name="libelleCreationGroupe" id="libelleCreationGroupe" class="creaGrp"></td>
				</tr>
				<tr>
					<td>Groupe père</td>
					<td>
						<!--<input type="text" name="libelleCreationGroupe" id="libelleCreationGroupe" class="creaGrp">-->



						<select name="listegroupe" id="listeGroupesPeres">
							<option value=""></option>
{% for groupe in tuplesGroupe %}
							<option value="{{groupe.getGroupe()}}">{{groupe.getGroupe()}}</option>
{% endfor %}
						</select>





					</td>
				</tr>
				<tr>
					<td><input type="button" class="option" value="Valider création" onclick="creerGroupe()"></td>
				</tr>
			</table>

			<form action="modificationsGroupes.php" method="post">
			<table class="cadre tableauAffecterGroupeTuteur">
				<tr>
					<th colspan="2">Affecter un tuteur à un groupe</th>
				</tr>
				<tr>
					<td>
						<select name="listetuteur" id="listetuteur">
{% for tuteur in tuplesTuteurs %}
							<option value="{{tuteur.getId()}}">{{tuteur.getNom()}} {{tuteur.getPrenom()}}</option>
{% endfor %}
						</select>
					</td>
					<td>
						<select name="listegroupe" id="listegroupe">
{% for groupe in tuplesGroupe %}
								<option value="{{groupe.getGroupe()}}">{{groupe.getGroupe()}}</option>
{% endfor %}
						</select>
					</td>
				</tr>
				<tr>
					<td><input type="submit" class="option" value="Valider ajout"></td>
				</tr>
			</table>
			</form>
			
			<form action="modificationsGroupes.php" method="post">
				<table class="cadre tableauSupprimerGroupeTuteur">
					<tr>
						<th colspan="2">Supprimer une affectation d'un tuteur à un groupe</th>
					</tr>
{% for tuple in tuplesGroupesTuteurs %}
					<tr>
						<td><input type="text" value="{{tuple.getId()}}" readonly></td>
						<td><input type="text" value="{{tuple.getGroupe()}}" class="groupeTuteur" readonly></td>
						<td><input type="button" class="option" value="Supprimer" onclick="supprimerTuteurGroupe('{{tuple.getId()}}','{{tuple.getGroupe()}}')"></td>
					</tr>
{% endfor %}
				</table>
			</form>
		</section>
	</main>

	<form name="formulaireRecharger" action="modificationsGroupes.php"><input type="hidden" name="" value=""></form> 

<script type="text/javascript">

	function supprimerTuteurGroupe(param1, param2) {
		if(confirm('Êtes-vous sûr de vouloir supprimer cette affectation de tuteur à un groupe ?')){
			$.ajax({
				url : 'ajaxQuery.php',
				type : 'POST',
				data :{
					myFunction : "supprTutGroupe",
					param :{
						p1:JSON.stringify(param1),
						p2:JSON.stringify(param2)
					}
				},
				success: function(data) {
					document.formulaireRecharger.submit();
				}
			})
		}
	}

	function changerAttribut() {
		var liste = document.getElementsByClassName("modif");
		for (var i=0; i<liste.length; i++) {
			if (liste[i].readOnly) {
				liste[i].removeAttribute('readonly');
				document.getElementById("modif01").value = "Terminer";
			}
			else{
				enregistrerModif();
			}
		}

		var liste2 = document.getElementsByClassName("gp");
		for (var i=0; i<liste2.length; i++) {
			if (liste2[i].disabled){
				liste2[i].removeAttribute('disabled');
			}
			else {
				liste2[i].setAttribute('disabled','');
			}
		}

		var liste3 = document.getElementsByClassName("sup");
		for (var i=0; i<liste3.length; i++) {
			if (liste3[i].style.visibility=="hidden"){
				liste3[i].style.visibility="initial";
			}
			else
				liste3[i].style.visibility="hidden";
		}

	}

	function enregistrerModif() {

		var groupePeres = document.getElementsByClassName("gp")[0];
		var listeModifGroupePere =  new Array();
		var listeModifGroupe = new Array();
		var listeModif = new Array();

		$.each($(".modif"),function() {
			listeModifGroupe.push($(this).val());
		});

		$.each($(".gp option:selected"), function(){
			listeModifGroupePere.push($(this).val());
		});

		listeModif.push(listeModifGroupe);
		listeModif.push(listeModifGroupePere);

		$.ajax({
			url : 'ajaxQuery.php',
			type : 'POST',
			data :{
				myFunction : "modifGroupes",
				param : {
					listeModif : JSON.stringify(listeModif)
				}
			},
			success: function(data){
				document.location.reload();
			}
		})
	}

	function supprimerGroupe(groupe) {
		if (confirm("Êtes-vous sûr de vouloir supprimer ce groupe ?\nCela supprimera aussi les groupes dont il est le père.")) {
			$.ajax({
				url : 'ajaxQuery.php',
				type : 'POST',
				data :{
					myFunction : "supprGroupe",
					param : {
						groupe : JSON.stringify(groupe)
					}
				},
				success: function(data) {
					document.location.reload();
				}
			})
		}
	}

	function creerGroupe() {
		var nouvGroupe = new Array();

		nouvGroupe.push(document.getElementById("libelleCreationGroupe").value);

		var listeGroupesPeres = document.getElementById("listeGroupesPeres");
		nouvGroupe.push(listeGroupesPeres.options[listeGroupesPeres.selectedIndex].text);

		$.ajax({
			url : 'ajaxQuery.php',
			type : 'POST',
			data : {
				myFunction : "creerGroupe",
				param : {
					nouvGroupe : JSON.stringify(nouvGroupe)
				}
			},
			success: function(data) {
				document.location.reload();
			}
		})
	}
</script>

{% include "pied.tpl" %}
