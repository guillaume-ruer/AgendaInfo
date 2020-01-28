<h1>Abonnement</h1>

<form action="str-abo.php" method="post" >
	<fieldset>
		<p>N'oubliez pas de valider les changements en cliquant sur "Valider" à la fin du formulaire.</p>

		<p><label>Payant : <input type="checkbox" name="payant" value="1" <?php checked($str->acc_payant() ) ?> /></label></p>

		<p>Structure de type <strong><?php echo structure::text_type($str->acc_type() ) ?></strong> pour une adhésion de 12 mois <?php echo structure::cout($str->acc_type() ) ?>€ 
			(+1€ chaque année).
		</p>
	</fieldset>

	<fieldset>
		<legend>Options</legend>
		<div id="model-option" >
			<?php model_option(new abo_option(['id'=> -1]) ) ?>
		</div>

		<div id="option" >
			<?php while($opt = $lso->parcours() ) : ?>
				<?php model_option($opt) ?>
			<?php endwhile ?>
		</div>

		<p><input id="ajt-option" type="button" value="Ajouter une option" /></p>
	</fieldset>

	<p><input type="hidden" name="ids" value="<?php $str->aff_id() ?>" />
	<input type="submit" name="ok" value="Valider" />
	</p>

</form>

<script>

$(function(){
	$('#model-option').hide(); 

	$('#option').on('click', '.option-sup', function(){
		if( confirm('Voulez vous vraiment supprimer cette option ? ') )
		{
			$(this).closest('.abo-option').slideUp(function(){
				$(this).remove(); 
			});
		}
	});

	$('#ajt-option').click(function(){
		var c = $('#model-option .abo-option').clone(); 
		c.find('[name="id[]"]').val(0); 
		c.hide(); 
		$('#option').append(c); 
		c.slideDown(); 
	});
});

</script>
