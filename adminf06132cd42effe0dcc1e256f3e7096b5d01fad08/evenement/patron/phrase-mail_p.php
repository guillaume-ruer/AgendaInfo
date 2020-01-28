<h1>Liste des phrases préconstruites pour les remarques</h1>

<?php pmess() ?>

<p><a href="phrase-mail-form.php" >Ajouter une phrase</a></p>

<table class="table_defaut" >
	<tr>
		<th>diminutif</th>
		<th>phrase</th>
		<td></td>
		<td></td>
	</tr>
<?php while($p = $ls->parcours() ) : ?>
	<tr>
		<td><?php echo $p->dim ?></td>
		<td><?php echo $p->phrase ?></td>
		<td><a href="phrase-mail-form.php?id=<?php echo $p->id ?>" >Mod</a></p>
		<td><a href="phrase-mail.php?id=<?php echo $p->id ?>" >Sup</a></p>
	</tr>
<?php endwhile ?>
</table>


