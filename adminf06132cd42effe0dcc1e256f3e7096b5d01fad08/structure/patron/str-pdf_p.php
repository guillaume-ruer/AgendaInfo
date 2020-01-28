<h1>Personnaliser le PDF</h1>

<?php pmess() ?>

<?php if( $str['id'] == 0) : ?>
	<p>Entête et pied de page page défaut.</p>
<?php endif ?>

<form action="" method="post" enctype="multipart/form-data" >
	
	<p>Entête (2480*572): <input type="file" name="ent" />
	<input type="hidden" name="MAX_FILE_SIZE" value="9999999" /><br />
	<input type="checkbox" name="ent_sup" /> : retirer l'entête.</p>

	<?php if( !empty($str['pdf_haut']) ) : ?>
		<p> Click droit affiché l'image pour voir en taille réel.<br />
		<img src="<?php echo $dos.$str['pdf_haut'] ?>" width="512px" height="143px" /></p>
	<?php endif ?>

	<p>Pied de page (2480*372): <input type="file" name="pied" />
	<input type="hidden" name="MAX_FILE_SIZE" value="9999999" />
	<br />
	<input type="checkbox" name="pied_sup" /> : retirer le pieds de page.</p>

	<?php if( !empty($str['pdf_bas']) ) : ?>
		<p> Click droit affiché l'image pour voir en taille réel.<br />
		<img src="<?php echo $dos.$str['pdf_bas'] ?>" width="512px" height="93px" />
		</p>

	<?php endif ?>

	<p><input type="hidden" name="ids" value="<?php echo $ids ?>" /></p>
	<input type="submit" name="ok" value="Valider" />
	</p>
</form>
