<?php

class fichier_form extends objet_base
{
	private $nom; 
	private $dos=C_DOS_PHP; 
	static private $num=0; 	
	private $id; 
	private $format='gif,jpg,jpeg'; 
	private $hauteur=0;
	private $largeur=0; 
	private $pdf2jpg=FALSE; 

	function __construct($do=array() )
	{
		parent::__construct($do); 
		$this->id = self::$num++; 
	}

	function mut_nom($nom){ $this->nom = $nom; } 
	function mut_dos($dos){ $this->dos = $dos; } 
	function mut_hauteur($hauteur){ $this->hauteur = $hauteur; } 
	function mut_largeur($largeur){ $this->largeur = $largeur; } 
	function mut_format($format){ $this->format = $format; } 
	function mut_pdf2jpg($pdf2jpg){ $this->pdf2jpg = $pdf2jpg; } 

	function aff()
	{
		$id = $this->id; 
		$dos = $this->dos; 
		$nom = $this->nom; 
		require C_PATRON.'fichier_form.php'; 
	}

	function donne()
	{
		$donne = array('nom' => '' ); 
		$name = 'fichier_'.$this->id; 

		$pdf2jpg = FALSE; 
	
		if( $this->pdf2jpg && !empty($_FILES[$name]['tmp_name']) )
		{
			$finfo = finfo_open(FILEINFO_MIME); 

			if (!$finfo) 
			{
			    echo "Opening fileinfo database failed";
			    exit();
			}

			$res = finfo_file($finfo, $_FILES[$name]['tmp_name']);
			finfo_close($finfo);
			
			list($type, $charset ) = explode(';', $res); 

			if( $type == 'application/pdf')
			{
				$pdf2jpg=TRUE; 
			}
		}

		if( $pdf2jpg )
		{
			$num = (int)rappel('imgid') + 1; 
			$fnom = preg_replace('`\.[a-z]+$`i', '-'.$num.'.jpg' , $_FILES[$name]['name']); 
			exec('convert "'.$_FILES[$name]['tmp_name'].'" -colorspace RGB -resize '
				.$this->largeur.'x'.$this->hauteur.' "'.$this->dos.$fnom.'"', $output, $return_var);	
			memor('imgid', $num); 
			$donne['nom'] = $fnom; 
		}
		elseif($nom = tcimg($name, $this->dos, $this->format, $this->largeur, $this->hauteur, TRUE) )
		{
			$donne['nom'] = $nom; 
		}
		elseif( !isset($_POST['fichier_sup_'.$this->id]) && isset($_POST['fichier_nom_'.$this->id]) )
		{
			$donne['nom'] = $_POST['fichier_nom_'.$this->id];
		}
		
		return $donne; 
	}
}
