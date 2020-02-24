{% extends "templateBase.tpl" %}

{% block contenu %}
	<header class="cadre haut">
		<h1 class="centreTitre">Changement de mot de passe</h1>
		<a href="https://di.iut.univ-lehavre.fr/pedago/index.xml"><img class="logo" src="images/logo.gif" alt="Département Informatique" /></a>
	</header>
	
	<p style="text-align: center; font-size: 1.2em;">Vous devez saisir un nouveau mot de passe.</p>
	<div class="cadre form">
		<form method="post">
			<table>
{% if code_erreur is defined %}
				<tr class="ligneForm">
					<td class="erreur" colspan="2">
{% if code_erreur == 1 %}
					Mot de passe invalide (Min. 2 majuscules, 2 minuscules, 2 caractères non lettres et 8 caractères au total)
{% elseif code_erreur == 2 %}
					Erreur de la confirmation du mot de passe
{% endif %}
				</td>
				</tr>
{% endif %}
				<tr class="ligneForm">
					<td colspan="2" style="padding:2px; text-align:center;">
						<input type="password" name="mdp" placeholder="Mot de passe" oncopy="return false;" onpaste="return false;" oncut="return false;">
					</td>
				</tr>
				<tr class="ligneForm">
					<td colspan="2" style="padding:2px; text-align:center;">
						<input type="password" name="confirmMdp" style="margin-bottom:1vh" placeholder="Confirmer le mot de passe" onCopy="return false;" onpaste="return false;" onCut="return false;">
					</td>
				</tr>
				<tr class="ligneForm">
					<td colspan="2" style="padding:2px; text-align:center;">
						<input type="reset"  class="option bouton" value="Annuler">
						<input type="submit" class="option bouton" value="Valider">
					</td>
				</tr>
			</table>
		</form>
	</div>
{% endblock %}