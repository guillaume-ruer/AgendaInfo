<h1>Extraction</h1>

<h2>Email</h2>

<p><a href="email.php" >Email,nom,prénom en csv des structures actives</a></p>

<h2>Groupes</h2>

<table>
	<tr>
		<th>Nom du groupe</th>
		<th>Evenements (xls)</th>
		<th>Adresses (xls)</th>
	</tr>
<?php while( $do = fetch($lsg) ) : ?>
	<tr>
		<td><?php echo secuhtml($do['nom']) ?></td>
		<td><a href="evenement.php?id=<?php echo (int)$do['id'] ?>" >Event</a></td>
		<td><a href="adresse.php?id=<?php echo (int)$do['id'] ?>" >Adresse</a></td>
	</tr>
<?php endwhile ?>
</table>
