{% extends "templateBase.tpl" %}

{% block contenu %}
{% set droits =['A', 'AE', 'E', 'T'] %}

	<header class="cadre">
		<h1>Modification des utilisateurs</h1>
	</header>

	<main>
	<table class="cadre largeur">
		<tr>
			<th>ID</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Rôle</th>
			<th>Créé le</th>
			<th>Màj le</th>
			<th colspan="4"></th>
		</tr>
{% for utilisateur in tuplesUtilisateurs %}
{% if editMod == "modifier" and utilisateurId == utilisateur.getId()%}
		<form action="modificationsUtilisateurs.php" method="get">
			<tr>
				<td><input type="text" name="utilisateurId" value="{{utilisateur.getId()}}"readonly/></td>
				<td><input type="text" name="nom" value="{{utilisateur.getNom()}}"/></td>
				<td><input type="text" name="prenom" value="{{utilisateur.getPrenom()}}"/></td>
				<td><select name="role_utilisateur">
					{% for droit in droits %}
						{% if droit == utilisateur.getRole()%}
							<option selected value="{{droit}}">{{droit}}</option>
						{% else %}
							<option value="{{droit}}">{{droit}}</option>
						{% endif %}
					{% endfor %}
				</select></td>
				<td>{{utilisateur.getCreation('d/m/Y')}}</td>
				<td>{{utilisateur.getMaj('d/m/Y')}}</td>
				<td><a href="modificationsUtilisateurs.php"><input type="button" name="annuler" value="Annuler"></a></td>
				<td><input type="submit" name="modification" value="Valider"/></td>
			</tr>
		</form>
{% else %}
		<tr>
{% if utilisateur.getRole()=="A"%}
			<td style="background-color:#aaa;">{{utilisateur.getId()}}</td>
{% elseif utilisateur.getRole()=="T"%}
			<td style="background-color:#fff;">{{utilisateur.getId()}}</td>
{% else %}
			<td style="background-color:#ddd;">{{utilisateur.getId()}}</td>
{% endif %}
			<td>{{utilisateur.getNom()}}</td>
			<td>{{utilisateur.getPrenom()}}</td>
			<td>{{utilisateur.getRole()}}</td>
			<td>{{utilisateur.getCreation('d/m/Y')}}</td>
			<td>{{utilisateur.getMaj('d/m/Y')}}</td>
			<td><a class="option lien" href="modificationsUtilisateurs.php?edit=modifier&utilisateurId={{utilisateur.getId()}}"> Modifier</a></td>
			<td><a class="option lien" onClick="return confirmSuppr();" href="modificationsUtilisateurs.php?edit=supprimer&utilisateurId={{utilisateur.getId()}}"> Supprimer</a></td>
			<td><a class="option lien" onClick="return confirmNewMdp();" href="modificationsUtilisateurs.php?edit=nouvMdp&utilisateurId={{utilisateur.getId()}}"> Générer un nouveau mot de passe</a></td>
{% if utilisateur.getMdpGenere() is not null %}
			<td>{{utilisateur.getMdpGenere()}}</td>
{% endif %}
		</tr>
{% endif %}
{% endfor %}
{% if editMod == "ajouter" %}
		<form action="modificationsUtilisateurs.php" method="get">
			<tr>
				<td><input type="text" placeholder="ID"     name="utilisateurId"/></td>
				<td><input type="text" placeholder="Nom"    name="nom"/></td>
				<td><input type="text" placeholder="Prénom" name="prenom"/></td>
				<td>
					<select name="role_utilisateur">
{% for droit in droits %}
						<option value="{{droit}}">{{droit}}</option>
{% endfor %}
					</select>
				</td>
				<td></td>
				<td></td>
				<td><a href="modificationsUtilisateurs.php"><input type="button" name="annuler" value="Annuler" class="option"></a></td>
				<td><input type="submit" name="creation" value="Valider" class="option"/></td>
			</tr>
		</form>
{% else %}
{% endif %}
	</table>
{% if erreur is not empty %}
	<p>{{erreur}}</p>
{% endif %}
	<a href="modificationsUtilisateurs.php?edit=ajouter">
		<input class="option" type="button" value="Ajouter un utilisateur" id="boutonAjoutUtilisateur">
	</a>
	<br />
	<a href="menuAdmin.php" class="home">
	<div class="container">		
		<img src="images/home.png" alt="home">		
		<div class="middle">
			<div class="texteMilieu">Retour au menu</div>
		</div>
	</div>
	</a>

	<script>
	function confirmSuppr() {
		return confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?');
	}

	function confirmNewMdp(){
		return confirm('Êtes-vous sûr de vouloir générer un nouveau mot de passe ?');
	}
	</script>
</main>
{% endblock %}
