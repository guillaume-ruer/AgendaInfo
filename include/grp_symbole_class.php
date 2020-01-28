<?php

class grp_symbole
{
	public $id = 0;
	public $nom = ''; 


	function init($id)
	{
		$donne = req('SELECT nom_fr nom, id FROM categories_grp WHERE id='.absint($id).' LIMIT 1 '); 

		if( $do = fetch($donne) )
		{
			$this->id = absint($id); 
			$this->nom = $do['nom']; 
		}
		else
		{
			return FALSE; 
		}
	}

	function enr()
	{
		return empty($this->id ) ? $this->ins() : $this->maj(); 
	}

	function ins()
	{
		if(droit(GERER_SYMBOLE) )
		{
			$p = prereq('INSERT INTO categories_grp( nom_fr )VALUES(?) '); 
			exereq($p, array($this->nom) ); 
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

	function maj()
	{
		if(droit(GERER_SYMBOLE) )
		{
			$p = prereq('UPDATE categories_grp SET nom_fr=? WHERE id=? LIMIT 1 '); 	
			exereq($p, array($this->nom, $this->id )); 
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}
	
	function sup($id)
	{
		if(droit(GERER_SYMBOLE) )
		{
			req('DELETE FROM categories_grp WHERE id='.absint($id).' LIMIT 1 ');
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

}
