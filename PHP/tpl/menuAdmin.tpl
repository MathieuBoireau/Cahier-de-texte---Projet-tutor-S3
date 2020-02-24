{% include "enTete.tpl" %}

	<header class="cadre">
		Menu principal : ADMINISTRATEUR
	</header>

	<p class="cadre logout">
		{% if 'E' in user.getRole() %}
		<a href="journalDeBord.php" class="changementCouleur">Journal de bord (Enseignant)</a>
		<a href="etatSeances.php" class="changementCouleur">Etat des séances (Enseignant)</a>
		{% endif %}
		<a href="logout.php" class="btnDeco">Déconnexion</a>
	</p>

	<main class="cadre">
		<section>
			<article>
				<a href="menuAdmin.php?choix=utilisateur">
					<input class="btnMenu option" type="submit" value="Modifier les paramètres des utilisateurs">
				</a>
			</article>
			<article>
				<a href="menuAdmin.php?choix=module">
					<input class="btnMenu option" type="submit" value="Modifier les paramètres des modules">
				</a>
			</article>
			<article>
				<a href="menuAdmin.php?choix=seance">
					<input class="btnMenu option" type="submit" value="Modifier les paramètres des séances et évènements">
				</a>
			</article>
			<article>
				<a href="menuAdmin.php?choix=groupe">
					<input class="btnMenu option" type="submit" value="Modifier les paramètres des groupes">
				</a>
			</article>
		</section>
	</main>

{% include "pied.tpl" %}
