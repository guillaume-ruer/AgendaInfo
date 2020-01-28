<h1>Hisorique</h1>

<?php trace_affiche_lien($TRACE_CONF) ?>

<?php $pagin->affiche() ?>

<table class="table_defaut" >
	<tr>
		<th>Type</th>
		<th>Texte</th>
		<th>Date</th>
		<th>Utilisateur</th>
		<th>Fichier</th>
		<th>Ligne</th>
	</tr>
<?php while( $t = $lstrace->parcours() ) : ?>
	<tr>
		<td><?php echo trace_nom_type($t->type, $TRACE_CONF) ?></td>
		<td><?php echo $t->texte ?></td>
		<td><?php echo $t->date ?></td>
		<td><?php echo $t->pseudo ?></td>
		<td><?php echo $t->fichier ?></td>
		<td><?php echo $t->ligne ?></td>
	</tr>
<?php endwhile ?>
</table>
