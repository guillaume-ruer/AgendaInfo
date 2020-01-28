<div class="oc_champ" >

<?php foreach( $tab_champ as $cle => $champ ) : ?>

<div>
	<div class="oc_case" ><label><?php $champ->aff_label_text() ?> : <?php $tab_cb[$cle.'_cb']->aff_champ() ?></label></div>

	<div class="oc_masquer" >
		<?php $champ->aff() ?>
	</div>
</div>

<?php endforeach ?>

</div>
