<?php
/*
	Cette fonction à besoin d'un fichier spécifique,
	Il comportera un nombre, incrémenté à chaque téléchargement, pour 
	l'unicité du nom de la photo téléchargé
*/

function my_upload($name_input, $doscible, array $type_fichier=array() )
{
	/*
		$_FILES vide?
	*/

	if(empty($_FILES[$name_input] ))
	{
		return FALSE;
	}

	/*
		Préparation des fichier/dossier utiles
	*/

	$dossier_upload = RETOUR.'dos-php/upload/';
	$fichier_conteur = RETOUR.'dos-php/upload/conteur';

	if(!file_exists($dossier_upload) )
	{
		mkdir($dossier_upload);
		touch($fichier_conteur);
	}	

	if(!file_exists($doscible) )
	{
		if(!mkdir($doscible) )
		{
			mess('Pas de dossier cible pour le téléchargement.');
			return FALSE;
		}
	}

	/*
		On détermine quel type de fichier peuvent être supporté
	*/
	
	if(empty($type_fichier) )
	{
		//Par défaut, des images 
		$masque = 'gif|png|jpg|jpeg';

	}
	else
	{
		$sep = $masque = '';

		foreach($type_fichier as $type)
		{
			$masque .= $sep.$type;
			$sep = '|';
		}
	}

	/*
		Initialisation de la boucle de téléchargement
	*/

	$num = (int)file_get_contents($fichier_conteur);
	$tab_nom_img = array();
	
	foreach($_FILES[$name_input]['error'] as $key => $error) 
	{
		if($error == UPLOAD_ERR_OK) 
		{
			$tmp_name = $_FILES[$name_input]['tmp_name'][$key];
			$nom = $_FILES[$name_input]['name'][$key];
			$type = $_FILES[$name_input]['type'][$key];
		 	
			if( preg_match('`'.$masque.'`', $type) )
			{
				$num++;
				
				$nomm = preg_replace('`(\.('.$masque.'))$`', 
					'_'.$num.'$1' , $nom );

				move_uploaded_file($tmp_name, $doscible.$nomm);
			
				$tab_nom_img[ $key ] = $nomm;

				$message = 'Fichier "%n" ajouté avec succès !';
				mess(str_replace('%n', $nom, $message ) ); 
			}
			else
			{
				//Movais type
				$message = 'Le type est invalide pour ce fichier : %t.';
				mess(str_replace('%t', $nom) );
				$erreur = TRUE;
			}
		}
		elseif($error != UPLOAD_ERR_NO_FILE )
		{
			mess("Erreur lors du téléchargement d'une image.");
		}
		
	}

	file_put_contents($fichier_conteur, $num);

	return $tab_nom_img;
}

/*
	Ajoute un filigranne à une image.
*/

function tamponne($image, $phrase='')
{
	if(empty($phrase) )
	{
		$phrase = 'copy-right '.NOM_SITE;
	}

	$size=getimagesize($image);

	$type = explode('/', $size['mime']); 
	$type = $type[1];

	$filigrane= iconv('UTF-8', 'ISO-8859-1', $phrase);

	$imagecreatefromtype = 'imagecreatefrom'.$type;

	$t=20; // Taille de la police
	$srcimage=  $imagecreatefromtype($image);

	$couleur_text = imagecolorallocatealpha($srcimage, 255, 255, 255, 100);
	$couleur_text2 = imagecolorallocatealpha($srcimage, 0, 0, 0, 100);

	$h=0;
	$l=0;
	$i=0;
	/*
		Cette double boucle permet de répeter le filigrane en 
		croisé sur toute l'image (suivant sa taille)
	*/

	$change = TRUE;
	while($h<$size[1])
	{
		while($l<$size[0])
		{
			if($change)
			{
				$couleur = $couleur_text;
				$change = FALSE;
			}
			else
			{
				$couleur = $couleur_text2;
				$change = TRUE;
			}

			imagestring($srcimage, $t, $l, $h+$t, $filigrane, $couleur);
			$l=$l+400;
		}
		
		$i++;
		$l = ($i%2) ? 200 : 0 ;
		$h=$h+100;
	}

	$imagetype = 'image'.$type;

	$imagetype($srcimage, $image); // l'image s'affiche

	//On supprime l'image pour libérer la mémoire
	imagedestroy($srcimage);

	return TRUE;
}

/*
	$type : extension séparé par une virgules, sans le point. 
	$redim : si la largeur et la hauteur sont donnée, indique si oui ou non on redimensionne l'image à ces valeurs. 
	$name : le nom de l'input type image 
	$dest : dossier ou sera enregistré l'image 
*/

function tcimg($name, $dest, $type=NULL, $largeur=NULL, $hauteur=NULL, $redim=FALSE )
{
	$do = $_FILES[$name]; 

	if( empty($do['name']) )
	{
		return FALSE; 
	}

	if( $do['error'] == UPLOAD_ERR_OK )
	{
		$verif = TRUE; 

		list($flargeur, $fhauteur ) = getimagesize($do['tmp_name']); 

		/*
			Vérification du type 
		*/ 
		if(!is_null($type) )
		{
			$tab_type = explode(',', $type ); 
			$vtype = FALSE; 

			foreach($tab_type as $t )
			{
				if( strpos($do['type'], trim($t)) !== FALSE )
				{
					$vtype = TRUE; 
					break; 
				}
			}

			if( !$vtype )
			{
				mess('Le type ne correspond pas. Le(s) type(s) attendu est(sont) : '.$type.'.' ); 
				return FALSE; 
			}
		}
		
		/*
			Vérification de la taille 
		*/
		$redimensionne = FALSE; 

		if( (!is_null($hauteur) && $hauteur != $fhauteur ) || (!is_null($largeur) && $largeur != $flargeur) )
		{
			if($redim )
			{
				$redimensionne = TRUE; 
			}
			else
			{
				mess('La taille de l\'image ne correspond pas. Hauteur attendu : '.$hauteur.', Largeur attendu : '.$largeur.'.');
				return FALSE; 
			}
		}

		
		/*
			Toute les vérification ont été effectué, Téléchargement de l'image
		*/

		$fnom = renom($do['name']); 

		mess('Le nom du fichier est : '.$fnom); 

		move_uploaded_file($do['tmp_name'], $dest.$fnom); 

		/*
			Si on a besoin d'un redimensionnement 
		*/

		if($redimensionne && strpos($do['type'], 'svg') === FALSE )
		{
			redim($dest.$fnom, $hauteur, $largeur ); 
		}
		
		return $fnom; 
	}
	elseif($do['error'] == UPLOAD_ERR_NO_FILE ) 
	{
		mess('Erreur lors du téléchargement de l\'image.'); 
		return FALSE;
	}
}

function redim($img, $hauteur, $largeur )
{
	if( preg_match('`\.jpe?g$`i', $img ) )
	{
		$ext = 'jpeg'; 
	}
	elseif( preg_match('`\.gif$`i', $img) )
	{
		$ext = 'gif'; 
	}
	elseif( preg_match('`\.png`', $img) )
	{
		$ext = 'png'; 
	}

	$create = 'imagecreatefrom'.$ext; 
	$save  = 'image'.$ext; 

	$source = $create($img); // La photo est la source
	
	$destination = imagecreatetruecolor($largeur, $hauteur); // On crée la miniature vide

	if( $ext == 'png') 
	{
		imagealphablending($destination, FALSE);
		imagesavealpha($destination, TRUE);
		$transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
		imagefilledrectangle($destination, 0, 0, $largeur, $hauteur, $transparent);
	}

	// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
	$largeur_source = imagesx($source);
	$hauteur_source = imagesy($source);
	$largeur_destination = imagesx($destination);
	$hauteur_destination = imagesy($destination);

	// On crée la miniature
	imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);

	// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
	$save($destination, $img);
}

function renom($chaine)
{
	$info = pathinfo($chaine);
	$ext = $info['extension'];
	$chaine = basename($chaine, '.'.$ext);
	$ext = strtolower($ext); 
	$chaine = utf8_encode(str_replace(str_split(utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ')),
		str_split('aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'), utf8_decode($chaine) ) );
	$chaine = strtolower($chaine); 
	$chaine = preg_replace('`[^a-z0-9]`', '_', $chaine); 
	$chaine = preg_replace('`_+`', '_', $chaine); 
	$chaine = trim($chaine, '_'); 

	if( !preg_match('`^[a-z]`',$chaine) )
	{
		$chaine = 'fichier_'.$chaine;
	}

	memor('imgid',$num = (int)rappel('imgid') + 1 );  

	return $chaine.'_'.$num.'.'.$ext;
}

