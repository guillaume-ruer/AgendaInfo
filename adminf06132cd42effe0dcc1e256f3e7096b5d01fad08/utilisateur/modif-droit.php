<?php
include '../../include/init.php';

non_autorise(MODIFIER_DROIT);

http_param( array(
	'idu' => 0,
	'p' => 0,
	'droit' => array()
) ); 

$edit = FALSE;
if(!empty($idu ) )
{
	$edit = TRUE;
	$m = new membre; 

	if( !$m->init($idu) )
	{
		header('Location:liste.php?p='.$p );
		exit(); 
	}

	if(isset($_POST['ok']) )
	{
		$m->droit = 0; 
		foreach($droit as $num )
		{
			$m->droit |= $TAB_DROIT[$num]['bit']; 	
		}

		$m->enregistre(); 
		mess("Les droits de $m->pseudo ont été modifié.");
	}
}
else
{
	header('Location:liste.php?p='.$p );
	exit(); 
}



include HAUT_ADMIN;
?>

<h1>Modification de droit</h1>

<p><a href="liste.php?p=<?php echo $p ?>" >Retour</a></p>

<?php pmess() ?>

<?php if($edit ) : ?>

<p>Droit de <strong><?php echo $m->pseudo ?></strong></p>

<form action="modif-droit.php" method="post" >

	<?php include 'patron/case-droit.php' ?>

	<p><input type="hidden" name="idu" value="<?php echo $idu ?>" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	<input type="submit" name="ok" value="Modifier !" />
	</p>
</form>

<?php endif ?>

<?php include BAS_ADMIN ?>
