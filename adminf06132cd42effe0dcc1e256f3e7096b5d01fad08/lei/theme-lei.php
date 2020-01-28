<?php
    
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 

// TRAITEMENT : 

if(isset($_GET['sup']) )
{
	$donne = req('DELETE FROM theme_lei WHERE id='.(int)$_GET['sup'].' LIMIT 1 ');
	
	if($donne->rowCount() == 1  )
	{
		mess('Correspondance supprimée ! '); 
	}
}

if( isset($_POST['ok']) )
{
	req('UPDATE theme_lei SET auto_actif=0'); 
	if( !empty($_POST['auto_actif']) )
	{
		req('UPDATE theme_lei SET auto_actif=1 
			WHERE id IN('.implode(',', array_map('intval', (array)$_POST['auto_actif']) ).') '); 
	}
	mess('Activation automatique mis à jour !'); 
}

// AFFICHAGE 

$theme = new reqa('
	SELECT secuhtml::nom_lei, secuhtml::CAT_NAME_FR AS nom_infolimo, absint::tl.id, absint::tl.auto_actif
	FROM theme_lei AS tl 
	LEFT JOIN Categories AS c 
		ON c.CAT_ID = tl.id_theme  
	WHERE tl.source='.$SOURCE.'
	ORDER BY nom_lei
'); 

include HAUT_ADMIN; 
?>

<h1>Correspondances avec les thèmes du <?php echo evenement::$TAB_SOURCE[$SOURCE]['nom_complet'] ?></h1>

<p><a href="theme-form.php" >Ajouter</a></p>

<?php pmess(); ?>

<form action="" method="post" >
<table class="table_defaut" >
	<tr>
		<th>Th&egrave;mes <?php echo evenement::$TAB_SOURCE[$SOURCE]['nom'] ?></th>
		<th>Th&egrave;mes Info Limousin</th>
		<th colspan="2" >Action</th>
		<th>Activation automatique</th>
	</tr>
<?php while( $th = $theme->parcours() ) : ?>
	<tr <?php echo ($theme->switch) ? 'class="ligne1"' : ''; ?> >
		<td><?php echo $th->nom_lei; ?></td>
		<td><?php echo $th->nom_infolimo; ?></td>
		<td><a href="theme-form.php?idt=<?php echo $th->id; ?>" >Modifier</a></td>
		<td><a href="theme-lei.php?sup=<?php echo $th->id; ?>" >Supprimer</a></td>
		<td><input type="checkbox" name="auto_actif[]" value="<?php echo $th->id ?>" <?php checked( $th->auto_actif==1 ) ?> /></td>
	</tr>
<?php endwhile; ?>

</table>
<p>Valider les thèmes activé automatiquement : <input type="submit" name="ok" value="Ok !" /></p>
</form>

<?php include BAS_ADMIN; ?>
