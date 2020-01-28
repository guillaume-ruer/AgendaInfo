<div class="og_groupe" >

<?php if( $vide ) : ?>

<div class="og_page" >
	<div class="og_titre" >
		<input id="<?php echo $onglet->acc_nom().'_vide' ?>"  
			type="radio" 
			name="<?php $onglet->aff_nom() ?>" 
			value="vide" 
			data-form-ext-empty="no-empty" 
			<?php checked( $defaut == 'vide' ) ?>/>
		<label for="<?php echo $onglet->acc_nom().'_vide' ?>" >
		<?php echo $vide_titre ?>
		</label>
	</div>

	<div class="og_masquer" >
		<p><?php echo $vide_message ?></p>
	</div>
</div>

<?php endif  ?>


<?php foreach( $tab_champ as $cle => $champ ) : ?>

<div class="og_page" >
	<div class="og_titre" >
		<input id="<?php echo $cle.'_'.$onglet->acc_identifiant() ?>"  
			type="radio" 
			name="<?php echo $onglet->acc_nom_champ() ?>" 
			value="<?php echo $cle ?>" 
			data-form-ext-empty="no-empty" 
			<?php checked( $defaut == $cle ) ?>/>
		<label for="<?php echo $cle.'_'.$onglet->acc_identifiant() ?>" >
		<?php echo $champ->label() ?>
		</label>
	</div>

	<div class="og_masquer" >
		<?php $champ->aff() ?>
	</div>
</div>

<?php endforeach ?>

</div>
