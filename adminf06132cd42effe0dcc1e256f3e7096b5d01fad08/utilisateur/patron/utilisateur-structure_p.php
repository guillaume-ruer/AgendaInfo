<h1>Accès aux droits</h1>

<?php include 'patron/menu_util.php' ?> 

<form action="utilisateur-structure.php" method="post" >
<fieldset>
	<legend>Liste des structures sur lesquels l'utilisateur a des droits</legend>
<table class="table_defaut" >
	<tr>
		<th>Login</th>
		<th>Modifier les événement</th>
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
	
	<p><input type="hidden" name="u" value="<?php echo $u ?>" />
	<input type="submit" name="ok" value="Modifiez !" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	<input type="hidden" name="rech" value="<?php echo $url_rech ?>" />
	</p>
</fieldset>
</form>

<form action="utilisateur-structure.php" method="post" >
<fieldset>
	<legend>Recherchez des structures à ajouter</legend>
	<p>Cherche dans login, nom et prénom : <input type="text" name="rech_u" value="" /><input type="submit" name="ok" value="Ok !" /></p>
	<p><input type="hidden" name="u" value="<?php echo $u ?>" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	<input type="hidden" name="rech" value="<?php echo $url_rech ?>" />
	</p>
</fieldset>
</form >

<?php if(!empty($rech_u) ) : ?>
<form action="utilisateur-structure.php" method="post" >
<fieldset>
	<legend>Ajouter des structures à la liste</legend>
	<table class="table_defaut" >
	<tr>
		<th>Login</th>
		<th>Modifier les événement</th>
		<th>Modifier la structure</th>
		<th>Gérer des droits</th>
	</tr>
	<?php for(;$r = $rech_u->parcours();$i++ ) : ?>
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

	<p><input type="hidden" name="u" value="<?php echo $u ?>" />
	<input type="submit" name="ok" value="Validez !" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	<input type="hidden" name="rech" value="<?php echo $url_rech ?>" />
	</p>
</fieldset>
</form>
<?php endif ?>
