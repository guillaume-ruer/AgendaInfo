<?php
require_once 'fonc_memor.php'; 

class transfert_form extends champ_form 
{
	protected $dos=C_DOS_PHP; 
	protected $poid = 100000; //1048576; 

	function verif()
	{
		return parent::verif(); 
	}

	function mut_donne($donne)
	{
		if( $donne instanceof fichier )
		{
			$this->donne = $donne; 
		}
	}

	function recup()
	{
		$this->donne = FALSE; 
		
		if( isset($_POST[$this->nom()]) && is_null($this->indice) )
		{
			$p = $_POST[$this->nom()]; 
		}
		elseif( isset($_POST[$this->nom()][$this->indice]) )
		{
			$p = $_POST[$this->nom()][$this->indice]; 
		}
		else
		{
			$p = array(); 
		}

		if( isset($_FILES[$this->nom()]) && is_null($this->indice) )
		{
			$f = $_FILES[$this->nom()]; 
		}
		elseif( isset($_FILES[$this->nom()]['name'][$this->indice]) )
		{
			$f = array(
				'name'=> $_FILES[$this->nom()]['name'][$this->indice], 
				'tmp_name'=> $_FILES[$this->nom()]['tmp_name'][$this->indice], 
				'size'=> $_FILES[$this->nom()]['size'][$this->indice], 
				'error'=> $_FILES[$this->nom()]['error'][$this->indice], 
				'type'=> $_FILES[$this->nom()]['type'][$this->indice]
			); 
		}
		else
		{
			$f = array(); 
		}

		$donne = array_merge($p, $f); 

		if( empty($donne) ) 
		{
			return FALSE; 
		}

		if( !file_exists($this->dos) )
		{
			if( !mkdir($this->dos) )
			{
				$this->mess( c('Dossier de destination invalide.'), 'message_erreur' ); 
				return FALSE; 
			}
		}

		if( !($finfo = finfo_open(FILEINFO_MIME) ) ) 
		{   
			exit('Impossible d\'ouvrir la base de données.'); 
		} 

		if( $donne['error'] == UPLOAD_ERR_OK )
		{
			if( ($fpoid = filesize($donne['tmp_name']) )> $this->poid )
			{
				$this->mess( c("Le fichier est trop lourd. Les fichiers dont le poid est inférieur à ".format_poid($this->poid)." sont autorisé."),
					'message_erreur' );
				return FALSE; 	
			}

			/*
				Type de fichier 
			*/
  

			$mime = finfo_file($finfo, $donne['tmp_name']); 
			list( $type, $stype ) = explode('/', $mime); 

			if( $type == 'image' )
			{
				$size = getimagesize($donne['tmp_name']); 
				$this->donne = new image( array(
					'width' => $size[0],
					'height' => $size[1],
					'mime' => $mime, 
					'poid' => $fpoid, 
				) ); 
			}
			else
			{
				$this->donne = new fichier( array(
					'mime' => $mime, 
					'poid' => $fpoid, 
				)); 
			}

			/*
				Tout est bon. 
			*/
			$fnom = self::renom($donne['name']); 

			if( move_uploaded_file($donne['tmp_name'], $this->dos.$fnom) )
			{
				$this->donne->src = (string)$fnom; 
				$this->donne->dos = $this->dos; 
			}
		}
		elseif( !isset($donne['sup']) && !empty($donne['nom']) && file_exists($this->dos.$donne['nom']) )
		{
			$mime = finfo_file($finfo, $this->dos.$donne['nom']); 
			list( $type, $stype ) = explode('/', $mime); 
			$fpoid = filesize($this->dos.$donne['nom']); 

			if( $type == 'image' )
			{
				$size = getimagesize($this->dos.$donne['nom']); 
				$this->donne = new image( array(
					'width' => $size[0],
					'height' => $size[1],
					'mime' => $mime, 
					'poid' => $fpoid, 
				) ); 
			}
			else
			{
				$this->donne = new fichier( array(
					'mime' => $mime, 
					'poid' => $fpoid, 
				)); 
			}

			$this->donne->src = (string)$donne['nom']; 
			$this->donne->dos = $this->dos; 
		}
	}

	static function renom($chaine)
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

		$ret = $chaine.'_'.$num; 

		if(!empty($ext) )
		{
			$ret .= '.'.$ext; 
		}

		return $ret;
	}

	function aff_label()
	{
		echo '<label for="'.$this->acc_identifiant().'" >'.$this->label;
		echo $this->chaine_requis().': </label>'; 
	}

	function aff_champ()
	{
		$nom = $this->acc_nom_champ(); 
		$do = $this->donne; 

		if( !empty($this->donne) )
		{
			$texte = "Cocher pour supprimer le fichier.\nInutile de cocher si vous choisissez un autre fichier avec le bouton parcourir"; 
			echo '<span class="form_ext_remove form_transfert_sup" >';
			$this->donne->aff();
			echo ' <label class="form_transfert_sup_label" >Supprimer <span data-tip="'.$texte.'" >?</span> 
				: <input type="checkbox" name="'.$nom.'[sup]" /></label>'; 
			echo '<input type="hidden" name="'.$nom.'[nom]" value="'.$do->src().'" /></span>'; 
		}

		echo '<input id="'.$this->acc_identifiant().'" type="file" name="'.$nom.'" />'; 
	}
}
