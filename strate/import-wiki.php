<?php


include '../include/init.php'; 
header('Content-type: text/html; charset=utf8');


$file = fopen('wiki_communes_limousin', 'r'); 
$nb_commune = 0;


$update = $BDD->prepare('UPDATE Lieu SET wikipedia= ? WHERE Lieu_ID = ? LIMIT 1 '); 
$donne = $BDD->prepare('SELECT Lieu_ID AS id, Lieu_Ville AS ville FROM Lieu WHERE Lieu_Ville LIKE ? LIMIT 1 ');

?>

<table>
	<tr>
		<th>Chaine de base</th>
		<th>La chaine de recherche</th>
		<th>Identifiant trouvé</th>
		<th>Ville correspondant à l'identifiant</th>
	</tr>
<?php
while( $ligne = fgets($file) )
{
	if(strpos($ligne, 'http:') !== FALSE )
	{
		$nb_commune ++;
		$recherche = preg_replace('`\(.+\)`', '', substr($ligne, 29) );
		$recherche = trim($recherche, " _\n" ); 
		$recherche = preg_replace('`-?(Saint)-?`', '%', $recherche );
		$recherche = str_replace('%27', '_', $recherche); 
		$donne->execute( array($recherche ) ); 
		$do = $donne->fetch();
		$id_lieu = ($donne->rowCount() > 1 ) ? '<strong>Plusieurs ville trouvé</strong>': (empty($do['id'])  ) ? '<strong>Pas trouvé !</strong>' : (int)$do['id'] ; 
		/*
		if( is_int($id_lieu) )
		{
			$update->execute(array( $ligne, $id_lieu ) ); 
		}
		*/
		?>
	<tr>
		<td> <?php echo $ligne; ?></td>
		<td><?php echo $recherche; ?></td>
		<td><?php echo $id_lieu ; ?></td>
		<td><?php echo (isset($do['ville']) )? secuhtml($do['ville']) : '' ; ?></td>
	</tr>
<?php 
		
	}
}

fclose($file);
?>

</table>

<p>
<?php echo $nb_commune; ?>
</p>
