<?php
require_once C_INC.'adresse_class.php'; 
require_once C_INC.'fonc_upload.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'proposition_interface.php'; 

class structure extends objet implements proposition
{
	// Etat 
	const ACTIF = 1;
	const INACTIF = 0; 
	const ATTENTE = 2; 

	// Visuel 
	const LOGO = 0;
	const BANNIERE = 1; 

	// Détection des modification 
	const M_LOGO = 0x1; 
	const M_NUM = 0x2; 
	const M_TYPE = 0x4; 
	const M_ACTIF = 0x8; 

	protected $id=0;
	protected $nom=''; 
	protected $modif = 0; // Savoir quel champ à été modifier 
	protected $numero = 0;
	protected $actif = self::ACTIF; 
	protected $droit=0; // Droit d'un utilisateur sur la structure 
	protected $logo = ''; 
	protected $banniere = ''; 
	protected $banniere_url = ''; 
	protected $adresse = '__adresse'; 
	protected $mail = ''; 
	protected $mail_rq = ''; 
	protected $desc = ''; 
	protected $type = ''; 
	protected $date = 0; 
	protected $tab_contact = array(); 
	protected $facebook = ''; 
	protected $code_externe=0; 
	protected $conv = '';
	protected $payant = FALSE; 
	protected $id_paypal = ''; 
	protected $date_fin_adhesion = 0; 
	protected $rappel = 0; 
	protected $rappel_facture = ''; 

	static public $tab_etat = [self::ACTIF=>'Actif', self::INACTIF=>'Inactif', self::ATTENTE => 'Attente']; 
	static public $tab_class_etat = [self::ACTIF=>'actif', self::INACTIF=>'inactif', self::ATTENTE => 'attente']; 
	static public $tab_type = [
		'particulier' => 'Particulier', 
		'autoent' => 'Auto-entrepreneur', 
		'association' => 'Association', 
		'societe' => 'Société', 
		'collectivite' => 'Collectivité',
	];

	static public $tab_type_meta =[
		'particulier' => [ 'cout' => 22, 'base' => 2016], 
		'autoent' => [ 'cout' => 22, 'base' => 2016], 
		'association' => [ 'cout' => 32, 'base' => 2016], 
		'societe' => [ 'cout' => 52, 'base' => 2016], 
		'collectivite' => [ 'cout' => 52, 'base' => 2016],
	]; 

	/*
		Mutateur 
	*/

	function mut_droit($d){ $this->droit = $d; } 
	function mut_numero($n)
	{ 
		$this->numero=$n; 
		$this->modif |= self::M_NUM; 
	}

	function mut_actif($a)
	{ 
		$this->actif=$a;
		$this->modif |= self::M_ACTIF; 
	} 

	function mut_logo($l, $t=FALSE )
	{ 
		if( $t )
		{
			if( $nom = str_upload(self::LOGO, $l) )
			{
				$this->logo = $nom; 
				$this->modif |= self::M_LOGO; 
			}
		}
		else
		{
			$this->logo = $l; 
			$this->modif |= self::M_LOGO; 
		}

	} 
	function mut_banniere($b){ $this->banniere = $b; }
	function mut_banniere_url($url){ $this->banniere_url = $url; } 
	function mut_mail($m){ $this->mail = $m; } 
	function mut_mail_rq($m){ $this->mail_rq = $m; } 
	function mut_desc($d){ $this->desc = $d; } 
	function mut_type($t)
	{ 
		$this->type = $t; 
		$this->modif |= self::M_TYPE; 
	} 

	function mut_date($d)
	{
		if(empty($d) || is_numeric($d) )
		{
			$this->date = $d; 
		}
		elseif( is_array($d) )
		{
			list($y, $m, $d) = explode('-', $d); 	
			$this->date = mktime(0, 0, 0, $m, $d, $y );
		}
	}
	
	function mut_tab_contact($contact)
	{
		$this->tab_contact = array(); 
		foreach( $contact as $c  )
		{
			$this->ajt_contact($c); 
		}
	}

	function ajt_contact($c)
	{
		if( is_array($c) )
		{
			$c = new contact($c); 
		}

		if( est_class($c, 'contact') && !$c->vide() )
		{
			$this->tab_contact[] = $c; 	
		}
	}
	function mut_facebook($fb){ $this->facebook=$fb; } 


	function mut_code_externe($code_externe){ $this->code_externe=(int)$code_externe; } 

	/*
		Accesseurs
	*/

	function acc_modif(){ return $this->modif; } 
	function acc_numero(){ return $this->numero; } 
	function acc_actif() { return $this->actif; }
	function acc_logo() { return $this->logo; }
	function acc_banniere(){ return $this->banniere; } 
	function acc_banniere_url(){ return $this->banniere_url; } 
	function acc_code_externe(){ return $this->code_externe; } 
	function acc_droit(){ return $this->droit; } 

	function acc_mail() { return $this->mail; }
	function acc_mail_rq() { return $this->mail_rq; } 
	function acc_desc(){ return $this->desc; } 
	function acc_type() { return $this->type; }
	function acc_date(){ return $this->date; } 
	function acc_tab_contact() { return $this->tab_contact; }
	function acc_facebook(){ return $this->facebook; } 

	function acc_contact($id=0)
	{
		return isset($this->tab_contact[$id]) ? $this->tab_contact[$id] : FALSE; 
	}

	function nb_contact()
	{
		return count($this->tab_contact); 
	}

	/*
		Afficheurs 
	*/

	function aff_desc($tronque=FALSE)
	{
		if($tronque)
		{
			$desc = $this->desc;
			$nbbr=6;
			if(substr_count($desc, "\n") >= $nbbr )
			{
				$numbr = 0; 	
				$pos = -1; 
				while( $numbr < $nbbr )
				{
					$pos = strpos($desc, "\n", $pos+1 ); 	
					$numbr++; 
				}
				$pos = ( $pos > 500 ) ? 500 : $pos - 1; 
				$desc = nl2br(secuhtml(substr($desc, 0, $pos ) ) ).' ...';
			}
			elseif(strlen($desc) > 500 )
			{
				$desc = nl2br(secuhtml(substr($desc, 0, 500 ) ) ).' ...';
			}
			else
			{
				$desc =nl2br(secuhtml($desc) ); 
			}
		}
		else
		{
			$desc = secuhtml($this->desc); 
		}

		echo $desc; 
	}

	function aff_date($mode=0)
	{
		if(!empty($this->date) )
		{
			echo date( ( $mode==0? 'Y-m-d' : 'd/m/y' ) , $this->date ); 
		}
	}

	function aff_date_fin_adhesion()
	{
		if(!empty($this->date_fin_adhesion) )
		{
			echo date('d/m/y', $this->date_fin_adhesion); 
		}
	}

	function aff_actif()
	{
		echo self::$tab_etat[ $this->actif ]; 
	}

	static function text_type($type)
	{
		return secuhtml(isset(self::$tab_type[ $type ]) ? self::$tab_type[ $type ] : '[inconnu: '.$type.']'); 
	}

	/*
		Fonction 
	*/

	static public function cout($type)
	{
		if( isset(self::$tab_type_meta[$type]) )
		{
			$m = self::$tab_type_meta[$type]; 
			return $m['cout'] + date('Y')-$m['base']; 
		}

		return FALSE; 
	}

	function abo_option()
	{
		$donne = req('SELECT id, description, structure structure__id, prix FROM abo_option WHERE structure='.(int)$this->id.' ');

		$opt=[]; 

		while( $do = fetch($donne) )
		{
			$opt[] = new abo_option(genere_init($do) ); 
		}

		return $opt; 	
	}

	function nom_normalise()
	{
		$chaine = $this->nom; 	
        $chaine = utf8_encode(str_replace(str_split(utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ')),
                str_split('aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'), utf8_decode($chaine) ) );
        $chaine = strtolower($chaine); 
        $chaine = preg_replace('`[^a-z0-9]`', '-', $chaine); 
        $chaine = preg_replace('`(^|-)(le|l|au|du|la|a-la|de-la|un|une|les|aux|des)(-|$)`','-', $chaine); 
        $chaine = preg_replace('`-+`', '-', $chaine); 
        $chaine = trim($chaine, '-'); 

		if(strlen($chaine) > 30 )
		{
			$chaine = substr($chaine, 0, 30); 
		}

        return $chaine; 
	}

	function cout_annuel()
	{
		$c = 0; 

		if( $this->payant )
		{
			$c += self::cout($this->type); 
		}

		$opt = $this->abo_option(); 

		foreach( $opt as $o )
		{
			$c += $o->acc_prix(); 
		}

		return $c; 
	}

	function modif($m)
	{
		return (bool)( $m & $this->modif ); 
	}

	/*
		Implément interface proposition 
	*/
	
	function json()
	{
		return json_encode( array('nom' => $this->acc_nom() ) ); 
	}

	function etiquette()
	{
		return $this->proposition(); 
	}

	function proposition()
	{
		return $this->acc_nom(); 
	}

}
