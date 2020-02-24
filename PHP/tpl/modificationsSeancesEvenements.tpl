{% include "enTete.tpl" %}
	<script src="javascript/jquery.js"></script>
	<script type="text/javascript" src="javascript/modificationsSeancesEvenements.js"></script>
	<header class="cadre">
		<h1>Modification des séances et évènements</h1>
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
		<section>
			<table class="cadreSimple">
				<tr>
					<th colspan="2">Types de séances</th>
				</tr>
				<tr>
					<td><input type="button" class="option" value="Modifier" onclick="changerAttribut()" id="modif01"></td>
				</tr>
				<tr>
					<td>
						<select name="selectTypeSeance" class="selectTypeSeance">
							{% for contrainte in contraintes %}
							{% if contrainte.getTab() == "seance" and contrainte.getType() == "type" %}
							<option value="{{contrainte.getValeur()}}">{{contrainte.getValeur()}}</option>
							{% endif %}
							{% endfor %}
						</select>
					</td>
					<td>
						<input type="button" value="Supprimer" class="sup option" style="visibility: hidden;" onclick="return supprTypeSeances()">
						<input type="button" value="Modifier" class="sup option" style="visibility: hidden;" onclick="return modifierTypeSeance()">
						<input type="text" name="nouvelleValeurTypeSeance" style="visibility: hidden;" id="nouvelleValeurTypeSeance" placeholder="Nouvelle valeur">
						<input type="button" class="option" value="Valider modification" onclick="modifierTypeSeanceValide()" style="visibility: hidden;" id="validerModification">
					</td>
				</tr>
				<tr>
					<td><input type="button" value="Ajouter" style="visibility:hidden;" class="boutonAjoutNouvelleSeance option" onclick="return ajoutTypeSeances()"></td>
					<td><input type="text" class="nouvelleSeance" style="visibility:hidden;"></td>
				</tr>
			</table>
			<form action="modificationsSeancesEvenements.php" method="post">
				<table class="cadreSimple">
					<tr>
						<td>Nombre maximum d'évènements par séance :</td>
						<td><input type="number" id="nbMaxEvtParAct" name="nbMaxEvtParAct"
							min="1" value="{{maxEvtParAct}}"></td>
					</tr>
					<tr>
						<td><input class="option" type="submit" name="changerMaxEvtParAct" value="Valider changement"></td>
					</tr>
				</table>
			</form>
			<form action="modificationsSeancesEvenements.php" method="post">
				<table class="cadreSimple">
					<tr>
						<td>Nombre maximum de séances affichables dans Etat séances :</td>
						<td><input type="number" id="nbMaxSeanceAff" name="nbMaxSeanceAff"
							min="1" value="{{maxSeanceAff}}"></td>
					</tr>
					<tr>
						<td><input class="option" type="submit" name="changerMaxSeanceAff" value="Valider changement"></td>
					</tr>
				</table>
			</form>
			<table class="cadreSimple">
				<tr><th colspan="2">Etats des sémaphores pour les séances</th></tr>
				<tr>
					<td>Etat Vu (Bouton / Texte): </td>
					<td><input type="color" name="couleurEtat1" value="{{couleurBoutonEtat2}}" id="couleurEtat1"> : <input type="color" value="{{couleurTexteEtat2}}" name="couleurEtat1Texte" id="couleurEtat1Texte"></td>
				</tr>
				<tr>
					<td>Etat Nouveau (Bouton / Texte):</td>
					<td><input type="color" name="couleurEtat2" value="{{couleurBoutonEtat1}}" id="couleurEtat2"> : <input type="color" value="{{couleurTexteEtat1}}" name="couleurEtat2Texte" id="couleurEtat2Texte"></td>
				</tr>
				<tr>
					<td colspan="2"><input type="button" class="option" value="Valider changement couleur" onclick="changerCouleurSemaphore()"></td>
				</tr>
			</table>
		</section>
		<section>
			<table class="cadreSimple">
				<tr>
					<th colspan="2">Types d'évènements</th>
				</tr>
				<tr>
					<td><input type="button" value="Modifier" class="option" onclick="changerAttribut2()" id="modif02"></td>
				</tr>
				<tr>
					<td>
						<select name="selectTypeEvt" class="selectTypeEvt">
							{% for contrainte in contraintes %}
							{% if contrainte.getTab()=="evenement" and contrainte.getType()=="type" %}
							<option value="{{contrainte.getValeur()}}">{{contrainte.getValeur()}}</option>
							{% endif %}
							{% endfor %}
						</select>
					</td>
					<td>
						<input type="button" value="Supprimer" class="sup2 option" style="visibility: hidden;" onclick="supprimerTypeEvenement()">
						<input type="button" value="Modifier" class="sup2 option" style="visibility: hidden;" onclick="modifierTypeEvenement()">
						<input type="text" name="nouvelleValeurTypeEvenement" style="visibility: hidden;" id="nouvelleValeurTypeEvenement" placeholder="Nouvelle valeur">
						<input type="button" id="validerModification2" class="option" value="Valider modification" style="visibility: hidden;" onclick="validerModif2()">
					</td>
				</tr>
				<tr>
					<td><input type="button" value="Ajouter" style="visibility:hidden;" class="boutonAjoutNouvelleSeance2 option" onclick="ajouterTypeEvenement()"></td>
					<td><input type="text" class="nouvelleSeance2" style="visibility:hidden;"></td>
				</tr>
			</table>
			<form action="modificationsSeancesEvenements.php" method="post">
				<table class="cadreSimple">
					<tr>
						<td>Nombre maximum de pièces jointes par évènement :</td>
						<td><input type="number" id="nbMaxPieceJointes" name="nbMaxPieceJointes"
							min="1" value="{{maxPieceJointe}}"></td>
					</tr>
					<tr>
						<td><input class="option" type="submit" id="boutonValiderPieceJointes" name="boutonValiderPieceJointes"
								value="Valider changement"></td>
					</tr>
				</table>
			</form>
		</section>
	</main>

{% include "pied.tpl" %}
