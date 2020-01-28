<h1>Droits des utilisateurs sur la structure</h1>

<form action="str-droit.php" method="post" >

<fieldset>
	<legend>Liste des utilisateurs ayant droits sur la structure</legend>
<table class="table_defaut" >
	<tr>
		<th>Login</th>
		<th>Modifier les événements</th>
		<th>Modifier la structure</th>
		<th>Gérer les droits</th>
	</tr>
<?php for($i=0; $do = $donne->parcours(); $i++ ) : ?>
	<tr>
		<td><?php echo $do->login ?></td>
		<td>
		<input type="checkbox" name="me[<?php echo $i ?>]" value="1" <?php if($do->droit & STR_EVENEMENT ) : ?>checked="checked"<?php endif ?> />
		</td>
		<td>
		<input type="checkbox" name="mi[<?php echo $i ?>]" value="1" <?php if($do->droit & STR_MODIFIER ) : ?>checked="checked"<?php endif ?>/> 
		</td>
		<td>
		<input type="checkbox" name="md[<?php echo $i ?>]" value="1" <?php if($do->droit & STR_DROIT ) : ?>checked="checked"<?php endif ?> />
		<input type="hidden" name="idu[<?php echo $i ?>]" value="<?php echo $do->id ?>" />
		</td>
	</tr>
<?php endfor ?>
</table>
	
	<p><input type="hidden" name="ids" value="<?php echo $ids ?>" />
	<input type="submit" name="ok" value="Modifiez !" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	</p>
</fieldset>
</form>

<form action="str-droit.php" method="post" >
<fieldset>
	<legend>Recherchez des utilisateurs</legend>
	<p>Cherche dans login, nom et prénom : <input type="text" name="rech" value="" /><input type="submit" name="ok" value="Ok !" /></p>
	<p><input type="hidden" name="ids" value="<?php echo $ids ?>" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	</p>
</fieldset>
</form >

<?php if(!empty($rech) ) : ?>
<form action="str-droit.php" method="post" >
<fieldset>
	<legend>Ajouter des utilisateurs ayant droits sur la structure</legend>
	<table class="table_defaut" >
	<tr>
		<th>Login</th>
		<th>Modifier les événements</th>
		<th>Modifier la structure</th>
		<th>Gérer des droits</th>
	</tr>
	<?php for(;$r = $rech->parcours();$i++ ) : ?>
		<tr>
			<td><?php echo $r->login ?></td>
			<td><input type="checkbox" name="me[<?php echo $i ?>]" value="1" /></td>
			<td><input type="checkbox" name="mi[<?php echo $i ?>]" value="1" /></td>
			<td><input type="checkbox" name="md[<?php echo $i ?>]" value="1" />
			<input type="hidden" name="idu[<?php echo $i ?>]" value="<?php echo $r->id ?>" />
			</td>
		</tr>
	<?php endfor ?>
	</table>

	<p><input type="hidden" name="ids" value="<?php echo $ids ?>" />
	<input type="submit" name="ok" value="Validez !" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	</p>
</fieldset>
</form>
<?php endif ?>
