<?php echo '<?xml version="1.0" encoding="utf8" ?>' ?>
<archive>
	<annee><?php echo $annee ?></annee>
	<nbevent><?php echo $evenement_total ?></nbevent>

<?php if( $lei = $stat_lei->parcours() ) : ?>
	<tab ancre="lei" >
		<nom >Evénement provenant du LEI</nom>
	<?php do { ?>
		<ent>
			<ch><?php echo $lei->nom; ?></ch>
			<ch><?php echo $lei->nb; ?></ch>
		</ent>
	<?php } while($lei = $stat_lei->parcours() ) ?>
	</tab>
<?php endif ?>

<tab ancre="departement" >
	<nom>Par département</nom>
	<nc>Département</nc>
	<nc>Nombre</nc>
<?php while($do = $departement->parcours() ) : ?>
	<ent>
		<ch><?php echo $do->dep ?></ch>
		<ch><?php echo $do->nbe ?></ch>
	</ent>
<?php endwhile ?>
</tab>

<tab ancre="contact" >
	<nom>Contact</nom>
	<nc>Id</nc>
	<nc>Adhérent</nc>
	<nc>Nombre</nc>

<?php while($co = $contact->parcours() ) : ?>
	<ent>
		<ch><?php echo $co->id ?></ch>
		<ch><?php echo empty($co->adh) ? 'sans_nom' : $co->adh ?></ch>
		<ch><?php echo $co->nbe ?></ch>
	</ent>
<?php endwhile ?>
</tab>

<tab ancre="categorie" >
	<nom>Catégorie</nom>
	<nc>Id</nc>
	<nc>Catégorie</nc>
	<nc>Nombre</nc>

<?php while($ca = $categorie->parcours() ) : ?>
	<ent>
		<ch><?php echo $ca->id ?></ch>
		<ch><?php echo empty($ca->nom ) ? 'sans_nom' : $ca->nom ?></ch>
		<ch><?php echo $ca->nbe ?></ch>
	</ent>
<?php endwhile ?>
</tab>

<tab ancre="theme" >
	<nom>Thème</nom>
	<nc>Id</nc>
	<nc>Thème</nc>
	<nc>Nombre</nc>

<?php while($th = $theme->parcours() ) : ?>
	<ent>
		<ch><?php echo $th->id ?></ch>
		<ch><?php echo empty($th->nom ) ? 'sans_nom' : $th->nom ?></ch>
		<ch><?php echo $th->nbe ?></ch>
	</ent>
<?php endwhile ?>
</tab>

<tab ancre="moderateur" >
	<nom>Modérateur</nom>
	<nc>Modérateur</nc>
	<nc>Nombre</nc>
<?php $totale=0; while($mod = $moderateur->parcours() ) : $totale+=$mod->nb ?>
	<ent>
		<ch><?php echo empty($mod->User) ? 'Sans nom' : $mod->User ?></ch>
		<ch><?php echo $mod->nb ?></ch>
	</ent>
<?php endwhile ?>
	<ent>
		<ch>Totale </ch>
		<ch><?php echo $totale ?></ch>
	</ent>

</tab>

</archive>
