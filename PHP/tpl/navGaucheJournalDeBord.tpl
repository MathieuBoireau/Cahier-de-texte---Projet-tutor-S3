<nav class="cadre partieGauche">
	{# <p class="titreJournalDeBord">JOURNAL DE BORD</p>
	{# <img src="./images/logo.gif" alt="logo" class="image"> #}
	{# </div> #}
		{# <a href="etatSeances.php" style="width:100%;">
		{# <img src="./images/rondNoir.png" alt="Filtrer les séances" style="width:10vh;" title="Filtrer les séances"> #}
		{# <input type="button" value="Vers l'état des séances" style="width:25vh; height:12vh;margin-bottom:6vh;font-size:2vh;"class="btnVerses option">
		</a>
		{% if 'A' in user.getRole() %}
		<a href="menuAdmin.php">
		<input type="button" value="Vers mode administrateur" style="width:25vh; height:8vh;margin-bottom:6vh;font-size:2vh;"class="btnVerses option">
		</a>
		{% endif %}
		<p class="option sansCurseur" style="text-align:left">{{user.getPrenom()}} {{user.getNom()}} : {{user.getRoleComplet()}}</p>
		<p style="text-align:left">
			<a href="logout.php">
				<input type="button" value="Déconnexion" style="width:25vh; height:8vh;margin-bottom:6vh;font-size:2vh;"class="option btnDeco">
			</a>
		</p> #}

	<div class="flecheEtMois">
		<a href="{{flecheG}}">
			<img src="./images/flecheGauche.png" class="fecheDiminue" alt="Mois précédent">
		</a>
		<h1>
			<a href="journalDeBord.php" class="mois">{{mois}} {{annee}}</a>
		</h1>
		<a href="{{flecheD}}">
			<img src="./images/flecheDroite.png" class="fecheDiminue" alt="Mois suivant">
		</a>
	</div>
	<div class="centrer" id="modif2">
		<div class="middle">
			<div class="texteHaut">Ajouter une séance</div>
		</div>
		<div class="home">
			<img src="./images/plus.png" alt="plus" class="boutonValModifGroupe">
		</div>
	</div>
</nav>
