<?php
require_once C_INC.'lien_grp_ls.php'; 
$LIEN_GRP = new lien_grp_ls(); 
$LIEN_GRP->requete(); 
?>
<div id="menu" >

<ul>
	<li><a class="bt-direct" href="<?php echo C_ADMIN; ?>admin.php" >Accueil</a></li>
	<li><span>Démarrer</span>
		<ul>
			<li class="menu-espace" ><a href="https://docs.google.com/present/edit?id=0AaoWMgL-qv0bZGZwd3BnZjlfOTVjNXhyOXdkYw&amp;hl=fr" target="_blank" >Notice</a></li>
			<li><a href="<?php echo C_ADMIN ?>article/liste-article.php?t=0" >Actualité</a></li>
			<li><a href="<?php echo RETOUR; ?>" >Aller à l'agenda</a></li>
		</ul>
	</li>
	<li><span>Evènement(s)</span>
		<ul>
			<li><a href="<?php echo C_ADMIN ?>evenement/event-form.php" >Ajouter un évènement</a></li>
			<li><a href="<?php echo C_ADMIN ?>evenement/event.php" >Liste évènement(s)</a></li>
		<?php if(droit(GERER_UTILISATEUR) ) : ?>
			<li class="menu-espace-haut" ><a href="<?php echo C_ADMIN; ?>evenement/gere-mail.php" >Texte remarque</a></li>
			<li><a href="<?php echo C_ADMIN; ?>evenement/phrase-mail.php" >Phrases préconstruites</a></li>
			<li><a href="<?php echo C_ADMIN; ?>evenement/symbole.php" >Symboles</a></li>
			<li><a href="<?php echo C_ADMIN; ?>evenement/grp-symbole.php" >Groupes de symboles</a></li>
			<li><a href="<?php echo C_ADMIN; ?>extraction/index.php" >Extraction de données</a></li>
		<?php endif ?>
		<?php if(droit(PREFIX) ) : ?>
			<li><a href="<?php echo C_ADMIN; ?>evenement/prefix.php" >Préfixe au titre</a></li>
		<?php endif ?>
		</ul>
	</li>
<?php if( droit(GERER_LIEU) ) : ?>
	<li><span>Lieux</span>
		<ul>
			<li><a href="<?php echo C_ADMIN ?>lieu/lieu.php" >Lieux</a></li>
			<li><a href="<?php echo C_ADMIN ?>lieu/lieu-form.php" >Ajouter un lieux</a></li>
			<li><a href="<?php echo C_ADMIN ?>lieu/grp-lieu.php" >Groupes de lieux</a></li>
			<li class="menu-espace" ><a href="<?php echo C_ADMIN ?>lieu/grp-lieu-form.php" >Ajouter un groupe</a></li>
			<li ><a href="<?php echo C_ADMIN ?>lieu/remarquable.php" >Remarquable</a></li>
		</ul>
	</li>
<?php endif ?>


	<li><span>Structure(s)</span>
		<ul>
			<?php if(!empty($MEMBRE->id_structure) ) : ?>
				<li class="menu-espace" ><a href="<?php echo C_ADMIN ?>structure/str.php?ids=<?php echo $MEMBRE->id_structure ?>" >Ma structure</a></li>
				<?php /*
				<li ><a href="<?php echo C_ADMIN ?>location/location.php?ids=<?php echo $MEMBRE->id_structure ?>" >Liste relais</a></li>
				*/ ?>
			<?php endif ?>

			<li><a href="<?php echo C_ADMIN ?>structure/str-liste.php" >Liste structure(s)</a></li>

		<?php if( droit(GERER_UTILISATEUR) ) : ?>
			<li class="menu-espace-haut" ><a href="<?php echo C_ADMIN ?>structure/str-form.php" >Nouvelle structure</a></li>
			<li><a href="<?php echo C_ADMIN ?>utilisateur/str-compte.php" >Adhésion</a></li>
			<li><a href="<?php echo C_ADMIN ?>location/location-admin.php" >Tous les relais</a></li>
			<li><a href="<?php echo C_ADMIN; ?>evenement/grp-structure.php" >Groupes de structure</a></li>
			<li><a href="<?php echo C_ADMIN ?>structure/str-pdf.php" >PDF Par défaut</a></li>
			<li><a href="<?php echo C_ADMIN ?>structure/mail-rappel.php" >Mail rappel</a></li>
		<?php endif ?>
		</ul>    
	</li>
<?php if( droit(GERER_UTILISATEUR) ) : ?>
	<li><span>Agenda Dynamique</span>
		<ul>
			<li><a href="<?php echo C_ADMIN; ?>dynagenda/aide.php" >Phrases Aide</a></li>
			<li><a href="<?php echo C_ADMIN; ?>dynagenda/planning.php" >Statistiques</a></li>
		</ul>
	</li>
	<li><span>Utilisateurs</span>
		<ul>
			<li><a href="<?php echo C_ADMIN ?>utilisateur/utilisateur-form.php" >Nouveau</a></li>
			<li><a href="<?php echo C_ADMIN ?>utilisateur/liste.php" >Liste</a></li>
			<li><a href="<?php echo C_ADMIN ?>trace/trace.php" >Historique</a></li>
		</ul>
	</li>

	<li><span>Visuels</span>
		<ul>
		<?php foreach($VISUEL_CONF as $VISUEL_ID_CONF => $VISUEL_DONNE ) : ?>
			<?php if( empty($VISUEL_DONNE['droit']) || droit($VISUEL_DONNE['droit']) ) : ?>
			<li><a href="<?php echo C_ADMIN ?>visuel/visuel.php?t=<?php echo $VISUEL_ID_CONF ?>" ><?php echo ucfirst($VISUEL_DONNE['nom']) ?></a></li>
			<?php endif ?>
		<?php endforeach ?>
		</ul>
	</li>
    
<?php endif ?>

<?php if(droit(GERER_LEI) ) : ?>
	<li><span>Import</span>
		<ul>
			<li>LEI</li>
			<li><a href="<?php echo C_ADMIN; ?>lei/theme-lei.php?s=<?php echo evenement::LEI ?>" >Thèmes</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/contact-lei.php?s=<?php echo evenement::LEI ?>" >Contact</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/log_lei.php?s=<?php echo evenement::LEI ?>" >Log</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/alerte.php?s=<?php echo evenement::LEI ?>" >Alerte</a></li>
			<li>SIRTAQUI</li>
			<li><a href="<?php echo C_ADMIN; ?>lei/theme-lei.php?s=<?php echo evenement::STQ ?>" >Thèmes</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/contact-lei.php?s=<?php echo evenement::STQ ?>" >Contact</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/log_lei.php?s=<?php echo evenement::STQ ?>" >Log</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/alerte.php?s=<?php echo evenement::STQ ?>" >Alerte</a></li>
		</ul>
	</li>
<?php endif ?>

<?php if( droit(GERER_VISUEL) ) : ?>
<?php /*
	<li><span>Liens</span>
		<ul>
			<li><a href="<?php echo C_ADMIN ?>lien/lien-form.php" >Ajouter un lien</a></li>
			<li class="menu-espace" ><a href="<?php echo C_ADMIN ?>lien/grp-lien.php" >Groupes de liens</a></li>
		<?php while( $LG = $LIEN_GRP->parcours() ) : ?>
			<li><a href="<?php echo C_ADMIN ?>lien/lien.php?grp=<?php $LG->aff_id() ?>" ><?php $LG->aff_nom() ?></a></li>
		<?php endwhile ?>
		</ul>
	</li>
*/ ?>
<?php endif ?>

<?php if( droit(CHANGER_FOND) || droit(TOUT_STAT) || droit(GERER_UTILISATEUR) ) : ?>
	<li><span>Site</span>
		<ul>
		<?php if( droit(CHANGER_FOND) ) : ?>
			<li><a href="<?php echo C_ADMIN; ?>fond/fond-form.php" >Image de fond</a></li>
		<?php endif ?>
		<?php if(droit(TOUT_STAT) ) : ?>
			<li><a href="<?php echo C_ADMIN; ?>stat/stat.php" >Stats générales</a></li>
			<li><a href="<?php echo C_ADMIN; ?>stat/stat_location.php" >Stats location</a></li>
		<?php endif ?>
		<?php if( droit(GERER_UTILISATEUR) ) : ?>
			<li><a href="<?php echo C_ADMIN ?>utilisateur/inscription.php" >Inscription</a></li>
		<?php endif ?>
		</ul>
	</li>
<?php endif ?>

<?php if( droit(GERER_EVENEMENT) ) : ?>
	<li><span>Mon compte</span>
		<ul>
				<a href="<?php echo C_ADMIN ?>compte/" >Email quotidien</a>
		</ul>
	</li>
<?php endif ?>

	<li><a class="bt-direct" href="<?php echo RETOUR ?>?dec=1" >Déconnexion du compte</a></li>
</ul>
</div>
