<?php

class symbole extends objet
{
	public $id=0;
	public $img='';
	public $nom='';
	public $width=0;
	public $height=0; 
	public $id_groupe=0;
	public $groupe = ''; 

	function aff()
	{
		if( !empty($this->img) )
		{
			$attr = ''; 
			if( !empty($this->width) ) 
			{
				$attr .= ' width="'.$this->width.'" '; 
			}
			
			if( !empty($this->height ) )
			{
				$attr .= ' height="'.$this->height.'" '; 
			}
			
			echo '<img src="'.C_IMG.'symboles/'.$this->img.'" '.$attr.' />';  
		}
	}

	function enr()
	{
		return empty($this->id) ? $this->ins() : $this->maj(); 
	}

	function ins()
	{
		if( droit(GERER_SYMBOLE) )
		{
			$pre = prereq('INSERT INTO Categories(CAT_NAME_FR, CAT_IMG, groupe,width,height)VALUES(?,?,?,?,?) ');
			exereq($pre, array($this->nom, $this->img, $this->id_groupe,$this->width,$this->height ) ); 
			$this->id = derid(); 
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

	function maj()
	{
		if( droit(GERER_SYMBOLE) )
		{
			$img = ''; 
			$tab = array($this->nom, $this->id_groupe,$this->width,$this->height) ;
			if( !empty($this->img) )
			{
				$img = ', CAT_IMG=?';
				$tab[] = $this->img; 
			}

			$tab[] = $this->id;
			$pre = prereq('UPDATE Categories SET CAT_NAME_FR=?, groupe=?,width=?,height=?'.$img.' WHERE CAT_ID=? LIMIT 1 '); 
			exereq($pre,$tab ); 
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

	function init($id)
	{
		$donne = req('SELECT CAT_IMG img, CAT_NAME_FR nom, groupe,width,height FROM Categories WHERE CAT_ID='.absint($id).' LIMIT 1 ');

		if( $do = fetch($donne) )
		{
			$this->img = $do['img'];
			$this->nom = $do['nom'];
			$this->width = $do['width'];
			$this->height = $do['height']; 
			$this->id_groupe = absint($do['groupe']); 
			$this->id = absint($id); 
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
			req('DELETE FROM Categories WHERE CAT_ID='.absint($id).' LIMIT 1 '); 
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}
}
