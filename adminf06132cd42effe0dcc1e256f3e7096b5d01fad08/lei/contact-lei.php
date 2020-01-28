<?php

include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 

/*
	TRAITEMENT 
*/
	
if(isset($_GET['sup']) )
{
	$donne = req('DELETE FROM contact_lei WHERE id_lei='.(int)$_GET['sup'].' LIMIT 1 '); 

	if($donne->rowCount() == 1 )
	{
		mess('Correspondance supprimée ! '); 
	}
}

/*
	AFFICHAGE 
*/

$donne = new reqa('
	SELECT secuhtml::id_lei, secuhtml::nom_lei, secuhtml::s.nom nom_infolimo, secuhtml::sc.titre, absint::id_infolimo 
	FROM contact_lei AS cl 
	LEFT JOIN structure_contact AS sc 
		ON cl.id_infolimo = sc.id 
	LEFT JOIN structure s
		ON s.id = sc.id_structure 
	WHERE cl.source='.(int)$SOURCE.'
'); 

include HAUT_ADMIN; 
?>

<h1>Correspondances des comptes <?php echo evenement::aff_source_nom($SOURCE) ?></h1>

<p><a href="contact-form.php" >Ajouter</a></p>

<?php pmess() ?>

<div class="table_defaut"> 
<table >
	<tr>
		<th>Id <?php evenement::aff_source_nom($SOURCE) ?></th>
		<th>Compte <?php evenement::aff_source_nom($SOURCE) ?></th>
		<th>Compte Info Limousin</th>
		<th>Id Info Limousin</th>
		<th colspan="2" >Action</th>

	</tr>
<?php while($do = $donne->parcours() ) : ?>
	<?php debug($do) ?>
	<tr class="<?php echo  $donne->switch ? 'ligne1' : 'ligne2' ; ?>" >
		<td><?php echo $do->id_lei; ?></td>
		<td><?php echo $do->nom_lei; ?></td>
		<td><?php echo $do->nom_infolimo; ?>
		<?php if( !empty($do->titre) ) : ?>
			<?php echo ', ',$do->titre ?>
		<?php endif ?>
		</td>
		<td><?php echo $do->id_infolimo; ?></td>
		<td><a href="contact-form.php?id=<?php echo $do->id_lei; ?>" >Modifier</a></td>
		<td><a href="contact-lei.php?sup=<?php echo $do->id_lei; ?>"
			onclick="return confirm('Voulez vous vraiment supprimer cette correspondance ?')" >Supprimer</a></td>
	</tr>
<?php endwhile ?>

</table>
</div>

<?php include BAS_ADMIN; ?>
