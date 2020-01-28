<?php
/*
	css 
	structure

	liste de cathégorie
	liste de thème 

	liste de lieux
	liste de groupe de lieux 

	liste de contact 
	liste de structure 
*/

class location 
{
	// Général
	public $id=0; 
	public $id_structure=0; 
	public $code=0;
	public $nom=''; 
	public $titre_flux=FALSE; 
	public $css=''; 

	// Idication filtre ( vocation de limité les requête ) 
	public $filtre=0x0;

	// Informations
	public $tabtheme=array(); 
	public $tablieux=array(); 
	public $tabgrplieux=array(); 
	public $tabstr=array(); 
	public $tabcontact=array(); 

	public $tab_ob_lieu = []; 

	// Filtre 
	public $lstheme=array(); 
	public $lslieux=array(); 
	public $lsgrplieux=array(); 
	public $lscontact=array();
	public $lsstr=array(); 

	function init($id )
	{
		$donne = prereq('SELECT * FROM Externe WHERE id=? LIMIT 1 '); 
		exereq($donne, array( $id ) ); 
		
		if( $do = fetch($donne) )
		{
			$this->id_structure = absint($do['structure']); 
			$this->code = absint($do['code']); 
			$this->nom = $do['nom']; 
			$this->css = $do['template']; 
			$this->id = $do['id']; 
			$this->filtre = absint($do['filtre']); 
			$this->titre_flux = (bool)$do['titre_flux']; 

			if($this->filtre & LOC_LIEUX ) 
			{
				$lieu = prereq('
					SELECT el.id_lieu, Lieu_Ville nom, Lieu_Dep dep 
					FROM Externe_lieux el
					LEFT JOIN Lieu l 	
						ON l.Lieu_ID = el.id_lieu 
					WHERE el.id_externe=? 
				'); 
				exereq($lieu, array($id) );

				while( $l = fetch($lieu) )
				{
					$this->lslieux[] = absint($l['id_lieu']); 
					$this->tablieux[] = secuhtml( $l['nom'].'('.$l['dep'].')' ); 
					$this->tab_ob_lieu[] = new ville(['id' => $l['id_lieu'], 'nom' => $l['nom'], 'dep' => ['num' => $l['dep'] ] ]); 
				}
			}

			if( $this->filtre & LOC_CONTACT ) 
			{
				$contact = prereq('
					SELECT ec.id_contact, s.nom, sc.titre, sc.tel, sc.site
					FROM Externe_contact ec 
					LEFT JOIN structure_contact sc 
						ON sc.id = ec.id_contact 
					LEFT JOIN structure s 
						ON s.id = sc.id_structure 
					WHERE ec.id_externe=?
				'); 
				exereq( $contact, array($id) ); 

				while( $c = fetch($contact) )
				{
					$this->lscontact[] = absint($c['id_contact']); 
					$this->tabcontact[] = secuhtml( $c['nom'].' : '.$c['titre'].' ('.$c['tel'].', '.$c['site'].' )'); 
				}
			}

			if( $this->filtre & LOC_THEME ) 
			{
				$donne = prereq('
					SELECT et.id_theme, cg.nom_fr nom 
					FROM externe_theme et
					LEFT JOIN categories_grp cg
						ON et.id_theme = cg.id
					WHERE et.id_externe=?
				'); 
				exereq( $donne , array($id) ); 

				while( $do = fetch($donne ) )
				{
					$this->lstheme[] = absint($do['id_theme']); 
					$this->tabtheme[] = secuhtml($do['nom']); 
				}
			}

			if( $this->filtre & LOC_GRPLIEUX ) 
			{
				$donne = prereq('
					SELECT eg.id_groupe, lg.Nom nom 
					FROM externe_grplieu eg
					LEFT JOIN Lieu_grp lg
						ON lg.id = eg.id_groupe
					WHERE eg.id_externe=?'); 
				exereq( $donne , array($id) ); 

				while( $do = fetch($donne ) )
				{
					$this->lsgrplieux[] = absint($do['id_groupe']); 
					$this->tabgrplieux[] = secuhtml($do['nom']); 
				}
			}

			if( $this->filtre & LOC_STR)
			{
				$donne = prereq('
					SELECT es.id_str, s.nom 
					FROM externe_str es
					LEFT JOIN structure s 
						ON s.id = es.id_str
					WHERE es.id_externe=?
				'); 
				exereq( $donne , array($id) ); 

				while( $do = fetch($donne ) )
				{
					$this->lsstr[] = absint($do['id_str']); 
					$this->tabstr[] = secuhtml($do['nom']);
				}
			}

			return TRUE; 
		}
		else 
		{
			return FALSE; 
		}
	}

	function enr()
	{
		if( empty($this->id) )
		{
			$this->ins(); 			
		}
		else
		{
			$this->maj(); 	
		}
	}

	function ins()
	{
		// On génère un code aléatoire et on s'assure qu'il soit unique. 
		$pre = prereq('SELECT id FROM Externe WHERE code=? LIMIT 1 '); 

		do
		{
			$code = rand(1,99999); 
			exereq( $pre, array($code) ); 
		}
		while( fetch($pre) ); 
		
		$this->mut_filtre(); 
		$ins = prereq('INSERT INTO Externe (code, template, nom, filtre, structure, titre_flux) VALUES(?,?,?,?,?, ?)'); 
		exereq( $ins, array($code, $this->css, $this->nom, $this->filtre, $this->id_structure, $this->titre_flux ) ); 
		$this->id = derid(); 
		$this->code = $code; 
		$this->ins_filtre(); 
	}

	function maj()
	{
		$this->ins_filtre(); 
		$maj = prereq('UPDATE Externe SET template=?, nom=?, filtre=?, titre_flux=? WHERE id=? '); 
		exereq( $maj, array($this->css, $this->nom, $this->filtre, $this->titre_flux, $this->id ) ); 
	}

	function menage( $idl )
	{
		$idl = is_array($idl) ? implode(',', array_map('absint', $idl) ) : absint($idl); 
		req('DELETE FROM Externe_lieux WHERE id_externe IN( '.$idl.' )'  ); 
		req('DELETE FROM Externe_contact WHERE id_externe IN( '.$idl.' )'); 
		req('DELETE FROM externe_grplieu WHERE id_externe IN( '.$idl.' )'); 
		req('DELETE FROM externe_theme WHERE id_externe IN( '.$idl.' )'); 
		req('DELETE FROM externe_str WHERE id_externe IN( '.$idl.' )'); 
	}

	function mut_filtre()
	{
		$filtre = 0; 

		if( !empty($this->lslieux) )
		{
			$filtre |= LOC_LIEUX; 
		}

		if( !empty($this->lscontact) )
		{
			$filtre |= LOC_CONTACT; 
		}

		if( !empty($this->lsgrplieux) )
		{
			$filtre |= LOC_GRPLIEUX ; 
		}

		if( !empty($this->lsstr) )
		{
			$filtre |= LOC_STR ; 
		}

		if( !empty($this->lstheme ) )
		{
			$filtre |= LOC_THEME ; 
		}

		$this->filtre = $filtre; 
	}

	function ins_filtre()
	{
		$idl = absint($this->id); 
		self::menage($idl); 
		$this->mut_filtre(); 
		$filtre=$this->filtre; 

		if( $filtre & LOC_LIEUX )
		{
			$value = $this->tab2value($this->lslieux) ; 	
			req('INSERT INTO Externe_lieux(id_externe, id_lieu) VALUES'.$value ); 
		}

		if( $filtre & LOC_CONTACT )
		{
			$value = $this->tab2value($this->lscontact); 
			req('INSERT INTO Externe_contact(id_externe, id_contact ) VALUES'.$value ); 
		}

		if( $filtre & LOC_GRPLIEUX )
		{
			$value = $this->tab2value($this->lsgrplieux ); 
			req('INSERT INTO externe_grplieu(id_externe, id_groupe ) VALUES'.$value ); 
		}

		if( $filtre & LOC_STR )
		{
			$value = $this->tab2value($this->lsstr); 
			req('INSERT INTO externe_str(id_externe, id_str ) VALUES'.$value ); 
		}

		if( $filtre & LOC_THEME )
		{
			$value = $this->tab2value($this->lstheme); 
			req('INSERT INTO externe_theme(id_externe, id_theme ) VALUES'.$value ); 
		}
	}

	function tab2value($tab)
	{
		list(,$c) = each($tab); 
		$value = "(".$this->id.", ".$c." )"; 	
		while( list(,$c) = each($tab) )
		{
			$value .=", (".$this->id.", ".$c." )"; 
		}

		return $value; 
	}

	function sup($id)
	{
		self::menage($id); 
		$id = is_array($id) ? implode(',', array_map('absint', $id) ) : absint($id); 
		req('DELETE FROM Externe WHERE id IN( '.$id.' ) '); 
	}
}
