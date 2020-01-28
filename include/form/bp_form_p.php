<span class="bp_boite" >
	<span class="bp_relatif" >

		<input id="bp_<?php $bp->aff_nom() ?>" type="text" name="bp_nom_<?php $bp->aff_nom() ?>" 
			value="<?php $bp->acc_multiple() ? '' : $bp->acc_donne()->aff_nom() ?>" autocomplete="off" />

		<span class="bp_proposition" id="mess_<?php echo $bp->aff_nom() ?>" ></span>
	</span>

	<?php if( !$bp->acc_multiple() ) : ?>
		<input type="hidden" id="bp_id_<?php $bp->aff_nom() ?>" name="bp_id_<?php $bp->aff_nom() ?>" 
			value="<?php $bp->acc_donne()->aff_id() ?>" />
	<?php else : ?>
		<span id="bp_multiple_<?php $bp->aff_nom() ?>" > 
			<?php foreach($bp->acc_donne() as $d ) : ?>
				<span class="bp_choi" >
					<?php ps($d['nom']) ?>
					<input type="hidden" name="bp_id_<?php $bp->aff_nom() ?>[]" value="<?php echo (int)$d['id'] ?>" />
					<a>X</a>
				</span>
			<?php endforeach ?>
		</span>
	<?php endif ?>
</span>

<script type="text/javascript" >
$(function(){ bp('<?php $bp->aff_nom() ?>', '<?php echo $bp->acc_fichier() ?>', <?php echo $bp->acc_multiple() ? 'true' : 'false' ?>); }); 
</script>
