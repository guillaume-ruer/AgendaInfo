<fieldset>
	<legend>Droits</legend>
<?php foreach($TAB_DROIT as $num => $droit ) : ?>
	<p><input type="checkbox" id="d<?php echo $num ?>" name="droit[]" 
		value="<?php echo $num ?>" 
		<?php echo $m->droit & $droit['bit'] ? 'checked="checked"' : '' ?> />
		<label for="d<?php echo $num ?>" > : <?php echo $droit['nom'] ?></label>
	</p>
<?php endforeach ?>
</fieldset>
