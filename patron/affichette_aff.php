<p class="affichette" >
	<a href="<?php echo $do->acc_affiche_url() ?>" >
	<img src="<?php echo C_IMG.'bandeaux/'.secuhtml($do->acc_affiche() ) ?>" alt="Affichette" /></a><br />

	<?php $do->aff_date() ?> :

	<strong><?php echo $do->aff_titre() ?></strong>

	<?php if( $do->acc_contact()->acc_id() != 0 ) : ?>
		contact : <?php ps( $do->acc_contact()->acc_structure()->acc_nom() ) ?>
		<?php ps( $do->acc_contact()->acc_titre() ) ?>
		<?php ps( $do->acc_contact()->acc_tel() ) ?>
		<?php if( $do->acc_contact()->acc_site() != '' ) : ?>	
			<a href="<?php $do->acc_contact()->aff_site() ?>" >Accéder au site</a><?php endif ?>. 
	<?php endif ?>


	<?php if( !$txt_entier && strlen(utf8_decode($do->acc_desc() ) ) > 250 ) : ?>
		<?php echo spesubstr($do->acc_desc(), 0, 250 ).' ...' ?>
		<a href="<?php echo RETOUR ?>page/affichettes<?php $do->aff_id() ?>.html" >Lire la suite</a>
	<?php else : ?>
		<?php echo $do->acc_desc() ?>
	<?php endif ?>
</p>
