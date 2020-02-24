{% include "enTete.tpl" %}

<header class="cadre">
	<h1>Etat des séances</h1>
</header>

<script src="javascript/jquery.js"></script>

<div class="cadre logout">
	<a href="journalDeBord.php" class="item changementCouleur"> Vers le journal de bord</a>
{% if 'A' in user.getRole() %}
	<a href="menuAdmin.php" class="item changementCouleur">Mode administrateur</a>
{% endif %}
	<div class="item">{{user.getPrenom()}} {{user.getNom()}} : {{user.getRoleComplet()}}</div>
	<div class="item btnDeco"><a href="logout.php">Déconnexion</a></div>
</div>
<main>
	<section class="sectionFiltre">
		<article>
		<h3  class="aEcarter cadre">Critères de séance</h3>
			<form action="etatSeances.php" method="get">
			<table class="cadre full">
				<tr>
					<td>Module</td>
					<td>
						<select name="selectModule">
							<option value="default" ></option>
						{% for module in tuplesModule %}
							{% if module.getValeurModule() == moduleCourant %}
								<option selected value="{{module.getValeurModule()}}">{{module.getValeurModule()}}</option>
							{% else %}
								<option value="{{module.getValeurModule()}}">{{module.getValeurModule()}}</option>
							{% endif %}
						{% endfor %}
						</select>
					</td>
				</tr>
				<tr>
					<td>Date de création entre le</td>
					<td>
						<table>
							<tr>
								<td>
									{% if dateSeanceDebut != null %}
										<input type="date" name="dateDebS" value="{{dateSeanceDebut}}">
									{% else %}
										<input type="date" name="dateDebS">
									{% endif %}
								</td>
								<td>et le</td>
								<td>
									{% if dateSeanceFin != null %}
										<input type="date" name="dateFinS" value="{{dateSeanceFin}}">
									{% else %}
										<input type="date" name="dateFinS">
									{% endif %}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select name="selectTypeSeance">
							<option value="default"></option>
						{% for typeSeance in contraintes %}
						{% if typeSeance.getValeur() == typeSeanceCourant%}
							<option selected value="{{typeSeance.getValeur()}}">{{typeSeance.getValeur()}}</option>
						{% else %}
							{% if typeSeance.getTab() == "seance" and typeSeance.getType() == "type"%}
								<option value="{{typeSeance.getValeur()}}">{{typeSeance.getValeur()}}</option>
							{% endif %}
						{% endif %}
						{% endfor %}
						</select>
					</td>
				</tr>
				<tr>
					<td>Groupe</td>
					<td>
						<select name="selectGroupe">
							<option value="default"></option>
						{% for groupe in groupes %}
						{% if groupe.getGroupe() == groupeCourant %}
							<option selected value="{{groupe.getGroupe()}}">{{groupe.getGroupe()}}</option>
						{% else %}
							<option value="{{groupe.getGroupe()}}">{{groupe.getGroupe()}}</option>
						{% endif %}
						{% endfor %}
						</select>
					</td>
				</tr>
				<tr>
					<td>Propriétaire</td>
					<td>
						<select name="selectProprio">
							<option value="default"></option>
						{% for utilisateur in utilisateurs %}
						{% if utilisateur.getId() == proprietaireCourant %}
							<option selected value="{{utilisateur.getId()}}">{{utilisateur.getPrenom()}} {{utilisateur.getNom()}}</option>
						{% else %}
							{% if utilisateur.getId() != "admin" %}
								<option value="{{utilisateur.getId()}}">{{utilisateur.getPrenom()}} {{utilisateur.getNom()}}</option>
							{% endif %}
						{% endif %}
						{% endfor %}
						</select>
					</td>
				</tr>
			</table>
		</article>

		<article>
		<h3 class="aEcarter cadre">Critères d'évènement</h3>
			<table class="cadre full">
				<tr>
					<td>Type</td>
					<td>
						<select name="selectTypeEvent">
							<option value="default"></option>
						{% for typeEvent in contraintes %}
						{% if typeEvent.getValeur() == typeEventCourant%}
							<option selected value="{{typeEvent.getValeur()}}">{{typeEvent.getValeur()}}</option>
						{% else %}
							{% if typeEvent.getTab() == "evenement" and typeEvent.getType() == "type"%}
								<option value="{{typeEvent.getValeur()}}">{{typeEvent.getValeur()}}</option>
							{% endif %}
						{% endif %}
						{% endfor %}
						</select>
					</td>
				</tr>
				<tr>
					<td>Échéance entre le</td>
					<td>
						<table>
							<tr>
								<td>
									{% if dateEventDebut != null %}
										<input type="date" name="dateDebE" value="{{dateEventDebut}}">
									{% else %}
										<input type="date" name="dateDebE">
									{% endif %}
								</td>
								<td>et le</td>
								<td>
									{% if dateEventFin != null %}
										<input type="date" name="dateFinE" value="{{dateEventFin}}">
									{% else %}
										<input type="date" name="dateFinE">
									{% endif %}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</article>
		
		<input type="submit" name="filtres" value="Appliquer filtres" style="margin-top: 2vh; padding: 0.5vh;" class="option">
		<!-- Retour au menu de création et modification -->
		</form>
	</section>

	<section class="affichageSeance">
	{% if message is empty%}
		<article>
			<table class="cadre full">
				<tr style="height: 5vh;">
					<th>Sem</th>
					<th>Module</th>
					<th>Date</th>
					<th>Type</th>
					<th>Groupe</th>
					<th>Propriétaire</th>
					<th></th>
				</tr>
				{% set dureeTotale = 0 %}
				<p style="visibility: hidden;" id="id06">{{idUser}}</p>
				{% for seance in seances %}
				{% set break = false %}
					{% for indice in tuplesSemaphore if not break %}
						{% if indice.getValeurModule() is same as (seance.getValeurModule()) and
							indice.getDateSeance()   is same as (seance.getDateSeance())   and
							indice.getTypeSeance()   is same as (seance.getTypeSeance())   and
							indice.getGroupe()       is same as (seance.getGroupe())       and
							indice.getIdUser()         is same as (seance.getIdUser()) and
							indice.getUtilisateur() is same as (idUser)
						%}
								<tr class="soulign">
								<td style="background-color:#aaa">{{seance.getSemaine()}}</td>
								<td style="color:{{couleurTexteEtat2}}; background-color:#{{seance.coulModule()}}" id="id01">{{seance.getValeurModule()}}</td>
								<td style="color:{{couleurTexteEtat2}};" id="id02">{{seance.getDateSeance('d/m/Y')}}</td>
								<td style="color:{{couleurTexteEtat2}};" id="id03">{{seance.getTypeSeance()}}</td>
								<td style="color:{{couleurTexteEtat2}};" id="id04">{{seance.getGroupe()}}</td>
								<td style="color:{{couleurTexteEtat2}};" id="id05">{{seance.getUser()}}</td>

								<td><input type="button" style="background-color: {{couleurBoutonEtat2}}; width: 3vh; height: 3vh;" class="etatSemaphore" onclick="changerCouleurSemaphore(this,
									'{{seance.getValeurModule()}}',
									'{{seance.getDateSeance()}}',
									'{{seance.getTypeSeance()}}',
									'{{seance.getGroupe()}}',
									'{{seance.getIdUser()}}',
									'{{couleurBoutonEtat1}}',
									'{{couleurBoutonEtat2}}'
									)" title="Vu"></td>
						{% set break = true %}
						{% endif %}
					{% endfor %}
					{% if not break %}
							<tr class="soulign">
							<td style="background-color:#aaa">{{seance.getSemaine()}}</td>
							<td style="color:{{couleurTexteEtat1}}; background-color:#{{seance.coulModule()}}" id="id01">{{seance.getValeurModule()}}</td>
							<td style="color:{{couleurTexteEtat1}};" id="id02">{{seance.getDateSeance('d/m/Y')}}</td>
							<td style="color:{{couleurTexteEtat1}};" id="id03">{{seance.getTypeSeance()}}</td>
							<td style="color:{{couleurTexteEtat1}};" id="id04">{{seance.getGroupe()}}</td>
							<td style="color:{{couleurTexteEtat1}};" id="id05">{{seance.getUser()}}</td>
							<td><input type="button" style="background-color: {{couleurBoutonEtat1}}; width: 3vh; height: 3vh;" class="etatSemaphore" onclick="changerCouleurSemaphore(this,
							'{{seance.getValeurModule()}}',
							'{{seance.getDateSeance()}}',
							'{{seance.getTypeSeance()}}',
							'{{seance.getGroupe()}}',
							'{{seance.getIdUser()}}',
							'{{couleurBoutonEtat1}}',
							'{{couleurBoutonEtat2}}'
							)" title="Nouveau"></td>
					{% endif %}
						</tr>
						{% set tmp = 0 %}
						{% for event in events %}
						{% if seance.estParentDe(event) and
						(
							((event.getTypeEvent() == typeEventCourant and
								(typeEventCourant != "Travail à faire" or
								(typeEventCourant == "Travail à faire" and (dateEventDebut == "" or dateEventFin == "" or (event.estEntreDates(dateEventDebut, dateEventFin) == 1)))
							)) or
						typeEventCourant == "default")) %}
							   {% set tmp = tmp+1 %}
							{% endif %}
						{% endfor %}

						{% if tmp >0 %}
						<tr class="evt-caption">
							<td></td>
							<td>N° activité</td>
							<td>Type de travail</td>
							<td>Descriptif</td>
							<td>Date rendu</td>
							<td>Durée</td>
							<td>Pièces jointes</td>
						{%endif%}

					{% set nb = 0 %}
					{% for event in events %}
                            {% if seance.estParentDe(event) and
                            (
								((event.getTypeEvent() == typeEventCourant and
									(typeEventCourant != "Travail à faire" or
									(typeEventCourant == "Travail à faire" and (dateEventDebut == "" or dateEventFin == "" or (event.estEntreDates(dateEventDebut, dateEventFin) == 1)))
								)) or
                            typeEventCourant == "default")) %}
						   {% set nb = nb+1 %}
								<tr>
									<td></td>
									<td>{{nb}}</td>
									<td>{{event.getTypeEvent()}}</td>
									<td style="word-wrap: break-word;max-width:20vh;">{{event.getLibEvent()}}</td>
									<td>{{event.getDateRendu('d/m/Y')}}</td>
									<td>
									{% if event.getDuree() is not empty %}
										{{event.getDuree()}} h
									{% set dureeTotale = dureeTotale + event.getDuree() %} 

									{% endif %}
									</td>
{% set pjs = event.getPiecesJointes() %}
									<td>
{% for pj in pjs %}
										<a href="pj/documents_{{pj.getDate()}}/{{pj.getNomFichier()}}" download="{{pj.getPj()}}">{{pj.getPj()}}</a>
										<br />
{% endfor %}
									</td>
							</tr>
						{% endif %}
					{% endfor %}
				{% endfor %}

				<tr>
					<td colspan="5"></td>
					<td style="padding-top: 3vh;"><b>Durée totale : {{dureeTotale}} h</b></td>
					<td></td>
				</tr>
			</table>
		</article>
	{% else %}
		{{message}}
	{% endif %}
	</section>
</main>

{% include "pied.tpl" %}

<script type="text/javascript">
	function hexToRgb(hex) {
		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		return result ? {
			r: parseInt(result[1], 16),
			g: parseInt(result[2], 16),
			b: parseInt(result[3], 16)
		} : null;
	}
	function changerCouleurSemaphore(entree,module,dateseance,typeseance,groupe,user, cbe1, cbe2) {
		var boolSemaphore;
		var r1,g1,b1;
		var r2,g2,b2;
		r1 = hexToRgb(cbe1).r;
		g1 = hexToRgb(cbe1).g;
		b1 = hexToRgb(cbe1).b;
		r2 = hexToRgb(cbe2).r;
		g2 = hexToRgb(cbe2).g;
		b2 = hexToRgb(cbe2).b;

		color1 = "rgb("+r1+", "+g1+", "+b1+")";
		color2 = "rgb("+r2+", "+g2+", "+b2+")";

		var utilis = document.getElementById("id06").innerText || document.getElementById("id06").textContent;
		if (entree.style.backgroundColor==color1){
			entree.style.backgroundColor=color2;
			boolSemaphore = true;
			$.ajax({
				url : 'ajaxQuery.php',
				type : 'POST',
				data :{
					myFunction : "ajouterSemaphoreVrai",
					param :{
						module:JSON.stringify(module),
						groupe:JSON.stringify(groupe),
						dateseance:JSON.stringify(dateseance),
						typeseance:JSON.stringify(typeseance),
						user:JSON.stringify(user),
						utilis:JSON.stringify(utilis)
					}
				},
				success: function(data) {
					document.location.reload();
				}
			})
		}
		else {
			entree.style.backgroundColor=color1;
			boolSemaphore = false;
			$.ajax({
				url : 'ajaxQuery.php',
				type : 'POST',
				data :{
					myFunction : "supprimerSemaphoreVrai",
					param :{
						module:JSON.stringify(module),
						groupe:JSON.stringify(groupe),
						dateseance:JSON.stringify(dateseance),
						typeseance:JSON.stringify(typeseance),
						user:JSON.stringify(user),
						utilis:JSON.stringify(utilis)
					}
				},
				success: function(data) {
					document.location.reload();
				}
			})
		}

	}
</script>
