{% extends "templateBase.tpl" %}

{% block contenu %}
	<header class="cadre haut">
		<h1 class="centreTitre">Connexion</h1>
		<a href="https://di.iut.univ-lehavre.fr/pedago/index.xml"><img src="images/logo.gif" alt="Département Informatique" class="logo" /></a>
	</header>

	<div class="cadre form">
		<form action="authentification.php" method="post">
			<table>
{% if code_erreur is defined and code_erreur == 4 %}
				<tr class="ligneForm">
					<td class="erreur" colspan = 2>
						Identifiant ou mot de passe erroné
					</td>
				</tr>
{% endif %}
				<tr class="ligneForm">
					<td colspan="2" class="droite">
						<i class="fas fa-user"></i>
						<input type="text" name="id" placeholder="Identifiant">
					</td>
{% if id is empty and code_erreur is defined and code_erreur b-and 1 %}
					<td class="erreur">Identifiant obligatoire</td>
{% endif %}
				</tr>
				<tr class="ligneForm">
					<td colspan="2" class="droite">
						<i class="fas fa-lock"></i>
						<input type="password" placeholder="Mot de passe" name="mdp">
					</td>
{% if mdp is empty and code_erreur is defined and code_erreur b-and 2 %}
					<td class="erreur">Mot de passe obligatoire</td>
{% endif %}
				</tr>
				<tr class="ligneForm">
					<td colspan="2">
						<input type="reset"  class="option bouton" value="Annuler">
						<input type="submit" class="option bouton" value="Valider">
					</td>
				</tr>
			</table>
		</form>
	</div>
{% endblock %}