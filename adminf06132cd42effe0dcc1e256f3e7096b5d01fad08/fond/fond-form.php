<?php
/*
	Par StrateGeyti
	Crée le 18/09/2010 
*/
include '../../include/init.php';
include C_INC.'fonc_memor.php'; 
$nom_fichier = 'fond_infolimo'; 

define('C_IMAGE_FOND', C_DOS_PHP.'image_fond/'); 
if(!file_exists(C_IMAGE_FOND) )
{
	mkdir(C_IMAGE_FOND); 
}

if(isset($_POST['ok']) )
{
	$nom_image =''; 
	$image = $_FILES['image']; 
	if($image['error'] == UPLOAD_ERR_OK )
	{
		if( preg_match('`jpg|jpeg|png|gif`i', $image['type'] ) )
		{
			if(move_uploaded_file( $image['tmp_name'], C_IMAGE_FOND.$image['name']) )
			{
				mess("Téléchargement ok ! ");
			}
			$nom_image = $image['name']; 
		}
		else
		{
			mess("Le format de l'image ne semble pas approprié."); 
		}
	}
	else
	{
		$nom_image = $_POST['fond']; 
	}
		
	memor($nom_fichier, $nom_image );
}


$image = rappel($nom_fichier); 

include HAUT_ADMIN;
?>

<h1>Modifier l'image de fond </h1>
<?php pmess(); ?>
<script type="text/javascript" >
function videz()
{
	vide('fond'); 
	vide('image'); 
}

function vide(nom)
{
	document.getElementById(nom).value=''; 
}
</script>

<form action="fond-form.php" method="post" enctype="multipart/form-data" >
	<p>Si l'image de fond est déjà dans le dossier "<?php echo C_IMAGE_FOND; ?>", veuillez entrer son nom : </p>
	<p><input id="fond" type="text" name="fond" value="<?php echo $image; ?>" /></p>
	<p>Sinon, envoyez votre image (jpeg, jpg, gif ou png) : </p>
	<p><input type="hidden" name="max_file_size" value="999999999" /></p>
	<input id="image" type="file" name="image" value="" />
	<p>Si vous voulez remettre l'image par défaut, <a href="#" onclick="javascript:videz(); return false;" >videz les chants</a>, puis validez.</p>
	<p><input type="submit" name="ok" value="Ok !" /></p>
</form>


<?php
include BAS_ADMIN;
?>
