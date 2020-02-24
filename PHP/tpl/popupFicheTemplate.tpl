{% include "enTete.tpl" %}
		<script type="text/javascript" src="javascript/jquery.js"></script>
		<script type="text/javascript" src="javascript/popupFiche.js"></script>

		<table id="seance" class="cadre">
			{% block seance %}
		{% if enCrea %}
			<tr>
				<td>
					Module :
					<select id='module' name="module">
						{% for module in modules %}
						{% if seance.getValeurModule() == module.getValeurModule() %}
						<option selected value="{{module.getValeurModule()}}">{{module.getValeurModule()}}</option>
						{% else %}
						<option value="{{module.getValeurModule()}}">{{module.getValeurModule()}}</option>
						{% endif %}
						{% endfor %}
					</select>
				</td>
				<td>
					{% if seance.getDateSeance() is null%}
					Date séance : <input type="date" name="date" value="{{date}}"/>
					{% else %}
					Date séance : <input type="date" name="date" value="{{seance.getDateSeance}}"/>
					{% endif %}
					<p hidden>Une date de séance est obligatoire</p>
				</td>
				{% if seance is defined %}
				<td id="user" value="{{seance.getIdUser()}}">
					Professeur : {{seance.getUser()}}
				</td>
				{% else %}
				<td id="user" value="{{user.getId()}}">
					Professeur : {{user.getPrenom()}} {{user.getNom()}}
				</td>
				{% endif %}
			</tr>
			<tr>
				<td>
					Type de la séance :
					<select name="typeSeance">
						{% for contrainte in contraintes %}
						{% if contrainte.getTab() == "seance" and contrainte.getType() == "type" %}
						{% if contrainte.getValeur() == seance.getTypeSeance() %}
						<option selected value = "{{contrainte.getValeur()}}">{{contrainte.getValeur()}}</option>
						{% else %}
						<option value = "{{contrainte.getValeur()}}">{{contrainte.getValeur()}}</option>
						{% endif %}
						{% endif %}
						{% endfor %}
					</select>
				</td>
				<td>
					Groupe :
					<select name="groupe">
						{% for groupe in groupes %}
						{% if groupe.getGroupe() == seance.getGroupe() %}
						<option selected value="{{groupe.getGroupe()}}">{{groupe.getGroupe()}}</option>
						{% else %}
						<option value="{{groupe.getGroupe()}}">{{groupe.getGroupe()}}</option>
						{% endif %}
						{% endfor %}
					</select>
				</td>
			</tr>
		{% else %}
			<tr>
				<td>Module : {{seance.getValeurModule()}}</td>
				<td>
					{% if seance.getDateSeance is null%}
					Date séance : {{date}}
					{% else %}
					Date séance : {{seance.getDateSeance}}
					{% endif %}
					<p hidden class="erreur">Une date de séance est obligatoire</p>
				</td>
				{% if seance is defined %}
				<td id="user" value="{{seance.getIdUser()}}">
					Professeur : {{seance.getUser()}}
				</td>
				{% else %}
				<td id="user" value="{{user.getId()}}">
					Professeur : {{user.getPrenom()}} {{user.getNom()}}
				</td>
				{% endif %}
			</tr>
			<tr>
				<td>Type de la séance : {{seance.getTypeSeance()}}</td>
				<td>Groupe : {{seance.getGroupe()}}</td>
			</tr>
		{% endif %}
		{% endblock %}
		</table>

		{% block evenementSeance %}
		{% set nb = 1 %}
		{% for evenement in seance.getEvents() %}
		<section id="{{evenement.getIdEvent()}}">
			<table class="cadre">
				<tr>
					<td>Évènement {{nb}}</td>
					{% set nb = nb+1 %}
					<td>
						Type d'événement :
						<select name="typeEvent">
							{% for contrainte in contraintes %}
							{% if contrainte.getTab() == "evenement" and contrainte.getType() == "type" %}
							{% if contrainte.getValeur() == evenement.getTypeEvent() %}
							<option selected value = "{{contrainte.getValeur()}}">{{contrainte.getValeur()}}</option>
							{% else %}
							<option value = "{{contrainte.getValeur()}}">{{contrainte.getValeur()}}</option>
							{% endif %}
							{% endif %}
							{% endfor %}
						</select>
					</td>
					<td>
						Description <input name="lib" type="text" value="{{evenement.getLibEvent()}}" maxlength="90" />
						<p hidden class="erreur">Une description est nécessaire</p>
					</td>
					<td>
						Date rendu : <input type="date" name="daterendu" value="{{evenement.getDateRendu()}}" />
						<p hidden class="erreur">Une date de rendu est obligatoire</p>
					</td>
					<td>
						Durée : <input name="duree" type="number" value="{{evenement.getDuree()}}" min="0" step="any" />
						<p hidden class="erreur">La durée doit être un nombre</p>
					</td>
{% set maxPj = 0 %}
{% for contrainte in contraintes %}
{% if contrainte.getTab() == "evenement" and contrainte.getType() == "nombre" %}
{% set maxPj = contrainte.getValeur() %}
{% endif %}
{% endfor %}
					<td rowspan="{{maxPj+1}}">
						Pièces jointes : <br />
						{% set j = 1 %}
						{% for pj in evenement.getPiecesJointes() %}
						<a href="pj/documents_{{pj.getDate()}}/{{pj.getNomFichier()}}" download="{{pj.getPj()}}">{{pj.getPj()}}</a>
						{% set j = j + 1 %}
						<img src="images/poubelle.png" style="height : 50px;" /><br/>
						{% endfor %}
						{% if j <= maxPj %}
                        {% for i in j..maxPj %}
						<input type="file" name="{{i-(j-1)}}" id="ev{{evenement.getIdEvent()}}pj{{i-(j-1)}}" />
                        {% endfor %}
						{% endif %}
					</td>
					<td>
						<input type="button" name="supprEvt" value="Supprimer" class="boutonSupprimerEvt option" id="{{evenement.getIdEvent()}}"/>
					</td>
				</tr>
			</table>
		</section>
		{% endfor %}
		{% endblock %}
		{% block newEvenement %}
{% set maxEvent = 0 %}
{% for contrainte in contraintes %}
{% if contrainte.getTab() == "seance" and contrainte.getType() == "nombre" and seance.getEvents()|length < contrainte.getValeur() %}
		<section id="+">
			<table id="evenement" class="cadre">
				<tr>
					<td>Nouvel événement</td>
					<td>
						Type d'événement :
						<select name="typeEvent">
							{% for contrainte in contraintes %}
							{% if contrainte.getTab() == "evenement" and contrainte.getType() == "type" %}
							<option value = "{{contrainte.getValeur()}}">{{contrainte.getValeur()}}</option>
							{% endif %}
							{% endfor %}
						</select>
					</td>
					<td>
						Description <input name="lib" type="text" value="" maxlength="90"/>
						<p hidden class="erreur">Une description est nécessaire</p>
					</td>
					<td>
						Date rendu : <input type="date" name="daterendu" value="" />
						<p hidden class="erreur">Une date de rendu est obligatoire</p>
					</td>
					<td>
						Durée : <input style="width: 10vh;" name="duree" type="number" value="" min="0" step="any" />
						<p hidden class="erreur">La durée doit être un nombre</p>
					</td>
					{% set maxPj = 0 %}
					{% for contrainte in contraintes %}
					{% if contrainte.getTab() == "evenement" and contrainte.getType() == "nombre" %}
					{% set maxPj = contrainte.getValeur() %}
					{% endif %}
					{% endfor %}
				</tr>
				<tr>
					<td rowspan="{{maxPj+1}}">
						Pièces jointes :
						{% for i in 1..maxPj %}
						<div>
							<input type="file" name="{{i}}" id="ev+pj{{i}}">
							<br/>
						</div>
						{% endfor %}
					</td>
				</tr>
			</table>
		</section>
{% endif %}
{% endfor %}
		{% endblock %}
		<aside>
			<input type="button" name="valid"  value="Valider" class="bouton option"/>
			<input type="button" name="cancel" value="Quitter" class="bouton option"/>
		</aside>
	</body>
</html>
