<h1>Planning</h1>

<h2>Nombre de génération de planning pdf par mois</h2>

<table class="table_defaut" >
	<tr>
		<th>Mois</th>
		<th>Nombre de planning générés</th>
	</tr>

<?php foreach($tab as list($date, $nb) ) : ?>
	<tr>
		<td><?php echo $date ?></td>
		<td><?php echo $nb ?></td>
	</tr>
<?php endforeach ?>

</table>

<h2>Evenements les plus choisis dans un planning</h2>

<table>
<?php while($e = $lse->parcours() ) : ?>
	<tr>
		<td><a href="<?php echo C_ADMIN ?>evenement/event-form.php?id_maj=<?php echo $e->acc_id() ?>" ><?php $e->aff_titre() ?></a></td>
		<td><?php echo $e->acc_nbp() ?></td>
	</tr>
<?php endwhile ?>
</table>
