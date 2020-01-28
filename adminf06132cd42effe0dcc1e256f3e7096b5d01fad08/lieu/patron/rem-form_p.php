
<div id="rem-form" >

<h1>Formulaire</h1>

<?php if($valide) : ?>
	<p>Modifications pris en compte ! </p>
<?php else : ?>
	<form action="<?php echo NOM_FICHIER ?>" method="post" >

		<p>Type : </p>

		<div>
			<?php foreach(remarquable::$TAB_TYPE as $remid => $rem ) :?>
			<div class="rem-groupe" >
				<label><img src="<?php echo C_IMG.'groupe-remarquable/'.$rem['img'] ?>" /><br />
				<?php echo $rem['nom'] ?><br />
				<input type="radio" name="type" value="<?php echo $remid ?>" <?php checked($remarquable->type() == $remid ) ?> />
				</label>
			</div>
			<?php endforeach ?>
		</div>
		
		<?php $form->aff() ?>

		<p><input type="submit" name="ok" value="Valider" /></p>

	</form>
<?php endif ?>
</div>
