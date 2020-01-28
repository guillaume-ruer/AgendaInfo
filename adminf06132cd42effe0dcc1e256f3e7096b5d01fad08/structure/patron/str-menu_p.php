<?php
http_param(array('p' => 0, 'ids' => 0 ) ); 
$ids = empty($ids) && ($str instanceof structure) ? $str->acc_id() : $ids; 
?>
<p id="str-menu" >
	<?php if( $str instanceof structure ) : ?>
		<?php $str->aff_nom() ?> : 
	<?php endif ?>

	<a href="<?php echo C_ADMIN ?>structure/str-liste.php?p=<?php echo $p ?>" >Retour à la liste</a>
	<a href="<?php echo C_ADMIN ?>structure/str.php?ids=<?php echo $ids ?>&amp;p=<?php echo $p ?>" >Voir la structure</a>
	<?php if( str_droit_utilisateur($ids, STR_MODIFIER) ) : ?>
	<a href="<?php echo C_ADMIN ?>structure/facture.php?ids=<?php echo $ids ?>&amp;p=<?php echo $p ?>" >Factures</a>
	<?php endif ?>

	<?php if( str_droit_utilisateur($ids, STR_DROIT) ) : ?>
		<a href="<?php echo C_ADMIN ?>structure/str-droit.php?ids=<?php echo $ids ?>&amp;p=<?php echo $p ?>" >Accès aux droits</a> 
	<?php endif ?>

	<?php if( str_droit_utilisateur($ids, STR_MODIFIER) ) : ?>
		<a href="<?php echo C_ADMIN ?>structure/str-form.php?ids=<?php echo $ids ?>&amp;p=<?php echo $p ?>" >Modifier la structure</a>
		<a href="<?php echo C_ADMIN ?>location/location.php?ids=<?php echo $ids ?>&amp;p=<?php echo $p ?>" >Relais</a>
	<?php endif ?>

	<?php if( droit(GERER_UTILISATEUR) ) : ?>
		<a href="<?php echo C_ADMIN ?>structure/str-pdf.php?ids=<?php echo $ids ?>&amp;p=<?php echo $p ?>" >PDF</a>
		<a href="<?php echo C_ADMIN ?>structure/str-abo.php?ids=<?php echo $ids ?>&amp;p=<?php echo $p ?>" >Adhésion</a>
	<?php endif ?>
</p>
