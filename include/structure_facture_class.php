<?php

class structure_facture extends objet 
{
	protected $id=0; 
	protected $structure = '__structure'; 
	protected $date=0;
	protected $somme=0.0; 

	protected $fichier=''; 
	static $m_fichier=[
		'crud' => CRUD_IMISV
	]; 

	protected $dossier=''; 
	static $m_dossier = [
		'crud' => CRUD_IMISV
	];

	protected $type=0; 

	const PAYPAL=0;
	const CHEQUE=1;
	const ESPECE=2; 
	const VIREMENT=3; 

	public static $tab_type = [ self::VIREMENT=>'virement', self::PAYPAL => 'paypal', self::CHEQUE => 'chèque', self::ESPECE=> 'espèce']; 

	function aff_somme()
	{
		echo (float)$this->somme.'€'; 
	}

	function aff_type()
	{
		echo self::$tab_type[$this->type]; 	
	}

	function aff_date()
	{
		if( !empty($this->date) )
		{
			echo date('d/m/Y', $this->date); 
		}
		else
		{
			echo 'inconnu'; 
		}
	}

	function acc_json()
	{
		return json_encode([
			'id' => $this->id,
			'date' => $this->date,
			'date_text' => date('d/m/Y', $this->date), 
			'somme' => $this->somme, 
			'url_fichier' => $this->url_fichier(),
			'type' => $this->type
		]);
	}

	function aff_ligne($bt_modif=TRUE)
	{
		echo '<tr data-id="'.$this->acc_id().'" >';
			echo '<td>'; 
			$this->aff_date();
			echo '</td>'; 
			echo '<td>'; 
			$this->aff_somme();
			echo '</td>'; 
			echo '<td>'; 
			$this->aff_type();
			echo '</td>'; 
			echo '<td>'; 
			if( $this->url_fichier() )
			{
				echo '<a class="facture-fichier" href="'.$this->url_fichier().'" >Obtenir le fichier</a>'; 
			}
			echo '</td>'; 
			if( $bt_modif )
			{
				echo '<td>'; 
					echo '<a class="facture-modif" href="#" >Modifier</a>'; 
					echo '<div style="display:none" class="facture-data-raw" >'; 
					echo $this->acc_json(); 
					echo '</div>'; 
				echo '</td>'; 
			}
		echo '</tr>';
	}

	function url_fichier()
	{
			if( !empty($this->dossier) && !empty($this->fichier) )
			{
				return ADD_SITE.D_ADMIN.'utilisateur/facture.php?f='.$this->id; 
			}

			return FALSE; 
	}
}
