<?php
/*
	Par StrateGeyti
	Crée le 07/09/2010 */
include '../../include/init.php';
include C_INC.'reqa_class.php'; 

http_param(array(
	'idt'=>0
) );


// TRAITEMENT : 

if(isset($_POST['ok'], $_POST['theme_infolimo'], $_POST['nom_lei'] ) )
{
	if($idt == 0 )
	{
		//Ajout 
		mess('Ajout d\'une correspondance'); 
		req('INSERT INTO theme_lei(id_theme, nom_lei, source) 
			VALUES('.(int)$_POST['theme_infolimo'].', \''.secubdd(trim($_POST['nom_lei']) ).'\', '.(int)$SOURCE.') ');
	}
	else
	{
		//Modification 
		mess('Modification d\'une correspondance'); 
		req('UPDATE theme_lei SET id_theme='.(int)$_POST['theme_infolimo'].', nom_lei=\''.secubdd(trim($_POST['nom_lei']) ).'\' WHERE id='.$idt.' LIMIT 1 '); 
	}
}

// AFFICHAGE  :

$donne = req('SELECT nom_lei, id_theme FROM theme_lei WHERE id='.$idt.' LIMIT 1  ');
if( $do = fetch($donne) )
{
	$nom_lei = secuhtml($do['nom_lei']); 
	$id_theme_infolimo = (int)$do['id_theme']; 
}
else
{
	$nom_lei = '';
	$id_theme_infolimo = 0;
}


$theme_infolimo = new reqa('SELECT secuhtml::CAT_NAME_FR AS nom, absint::CAT_ID AS id FROM Categories ORDER BY nom '); 


include HAUT_ADMIN;
?>
<h1>Ajout d'une correspondance de th&egrave;me</h1>

<p><a href="theme-lei.php" >Retour</a></p>

<?php pmess(); ?>

<form action="theme-form.php" method="post" >

<p>Nom du th&egrave;me <?php echo evenement::$TAB_SOURCE[$SOURCE]['nom'] ?> : 
  <input type="text" name="nom_lei" value="<?php echo $nom_lei; ?>" /></p>

<select name="theme_infolimo" >
<?php while( $thil = $theme_infolimo->parcours() ) : ?>
	<option value="<?php echo $thil->id; ?>" <?php echo $id_theme_infolimo == $thil->id ? 'selected="selected"' : '' ; ?> ><?php echo $thil->nom; ?></option>
<?php endwhile; ?>
</select>

<p><input type="hidden" name="idt" value="<?php echo $idt; ?>" />
<input type="submit" name="ok" value="Envoi" /></p>

</form>

<?php
include BAS_ADMIN;
?>
