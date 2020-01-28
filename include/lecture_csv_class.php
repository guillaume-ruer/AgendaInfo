<?php 

class lecture_csv extends objet
{
	protected $fichier = ''; 
	protected $rsc = NULL; 
	protected $sep = ','; 
	protected $entete = []; 	

	function __construct($fichier, $sep=',')
	{
		$this->fichier = $fichier; 
		$this->sep = $sep; 

		$this->rsc = fopen($fichier, 'r'); 

		$entete = fgetcsv($this->rsc, NULL, $this->sep); 

		foreach($entete as $ent )
		{
			$ent = preg_replace('`[^a-z0-9]`i', '_', strtolower($ent) ); 
			$i=2; 
			$tmp = $ent; 

			while( in_array($ent, $this->entete ) )
			{
				$ent = $tmp.$i; 
				$i++; 
			}

			$this->entete[] = $ent; 
		}
	}

	function aff_entete()
	{
		var_dump($this->entete); 
	}

	function suiv()
	{
		if( $d = fgetcsv($this->rsc, NULL, $this->sep) )
		{
			$r = []; 

			foreach($this->entete as $id => $nom )
			{
				$r[ $nom ] = $d[$id]; 
			}
		}
		else
		{
			$r = FALSE; 
		}

		return $r; 
	}

	function close()
	{
		fclose($this->rsc);
	}
}
