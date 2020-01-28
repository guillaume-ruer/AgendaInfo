<h1>Factures de : <?php $str->aff_nom() ?></h1>

<?php if( !empty($str->acc_rappel_facture() ) ) : ?>
	<p><a href="<?php echo C_ADMIN.'utilisateur/facture.php?f='.$str->acc_rappel_facture() ?>" >Facture générée lors du rappel</a>.</p>
<?php endif ?>

<?php if( droit(GERER_UTILISATEUR) ) : ?>
    <p><a href="<?php echo C_ADMIN.'structure/gen-facture.php?id='.$str->id() ?>" >Générer une facture</a></p>
<?php endif ?>

<table class="ls-facture" >
	<thead>
		<tr>
			<th>Date</th>
			<th>Somme</th>
			<th>Type</th>
			<th>Fichier</th>
		</tr>
	<thead>
	<tbody>
	<?php while($f = $lsf->parcours() ) :  ?>  
		<?php $f->aff_ligne(FALSE) ?>
	<?php endwhile ?>
	</tbody>
</table>

