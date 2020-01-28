<?php

class structure_form
{
	public $str=NULL; 
	private $recup=FALSE; 
	private $ch_lieu=NULL; 

	function __construct()
	{
		$this->str = new structure; 
		$this->ch_lieu = new barre_proposition_form(['fichier'=> 'lieu.php', 'class' => 'ville', 'label' => 'Commune ', 'limite' => 1] ); 
	}

	function valide()
	{
		global $PAT; 
		$traitement = TRUE; 
		$str = $this->donnee(); 

		if( $str->nb_contact() == 0 ) 
		{   
			$PAT->ajt_mess("Vous devez mettre au moins un contact."); 
			$traitement = FALSE; 
		}   

		if( !$str->acc_nom() )
		{
			$PAT->ajt_mess("Le nom ne doit pas être vide.");
			$traitement = FALSE;
		}

		return $traitement; 
	}

	function mut_str($str)
	{
		if( est_class($str, 'structure') )
		{
			$this->str = $str; 
		}
		elseif( !($this->str = str_init($str) ) )
		{
			$this->str = new structure; 
		}

		return $this->str; 
	}

	function donnee()
	{
		if( $this->recup )
			return $this->str; 
		$this->recup=TRUE; 

		$str = $this->str;
		$traitement = FALSE; 
		$valide = TRUE; 

		list( $ids, $s_nom, $s_addr, $s_mail, 
			$s_mail_rq, $type, $idcsup, $presentation, 
			$tel, $titre, $site, 
			$idc, $s_actif, $s_sup_logo, $s_numero, $s_conv, $s_payant ) 
			= http_param_tab(
		array('ids'=>0, 's_nom', 's_addr', 's_mail', 
			's_mail_rq', 'type', 'idcsup' => array(), 'presentation', 
			'tel' => array(), 'titre' => array(), 'site' => array(), 
			'idc' => array(), 's_actif' => 0, 's_sup_logo', 's_numero' => 0,
			's_conv' => '', 's_payant' => FALSE
		)); 

		$s_nom=trim($s_nom);

		$tmp = $this->ch_lieu->donne(); 

		$s_ville = !empty($tmp) ? $tmp[0] : []; 

		// Champs de base 
		$str->hydrate(array(
			'id' => $ids,
			'nom' => $s_nom, 
			'adresse' => array( 
				'ville' => $s_ville, 
				'rue' => $s_addr
			),
			'mail' => $s_mail,
			'mail_rq' => $s_mail_rq, 
			'desc' => $presentation, 
			'conv' => $s_conv, 
			'payant' => $s_payant
		) ); 

		// Uniquement les champs modifiable avec des droits 
		if(droit(GERER_UTILISATEUR ) )
		{
			$str->mut_type($type);
			$str->mut_actif($s_actif); 
			$str->mut_numero($s_numero); 
		}

		if( $s_sup_logo )
		{
			$str->mut_logo(''); 
		}

		$str->mut_logo('s_logo', TRUE); 

		$idc = array_diff($idc, $idcsup); 

		foreach($idc as $i => $id )
		{
			$c = new contact(array(
				'id' => $id, 
				'titre' => $titre[$i], 
				'tel' => $tel[$i],
				'site' => $site[$i],
			) ); 

			$str->ajt_contact($c); 
		}

		return $str; 
	}

	function affiche()
	{
		global $PAT; 

		// Champ de séléction de lieu 

		$ville = new reqa('
			SELECT absint::Lieu_ID id, secuhtml::Lieu_Ville ville, absint::Lieu_Dep dep
			FROM Lieu ORDER BY Lieu_Dep, Lieu_Ville 
		'); 
		
		$ch_lieu = $this->ch_lieu; 
		$str = $this->str; 

		if( $str->acc_adresse()->acc_ville()->acc_id() != 0 )
		{
			$ch_lieu->mut_donne([ $str->acc_adresse()->acc_ville() ]); 
		}

		include C_ADMIN.'structure/include/tab-type-str.php'; 
		include C_ADMIN.'structure/patron/str-form.p.php'; 
	}
}
