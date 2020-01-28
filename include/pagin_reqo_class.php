<?php
class pagin_reqo extends objet_base 
{
	private $max_lien=31; 
	private $mode=self::NORMAL; 
	private $num_page=0; 
	private $nb_page=0; 
	private $url=''; 
	private $avec_p=TRUE; 

	const NORMAL=0;
	const COUPE=1;
	
	function __construct()
	{
		$this->mut_url(FICHIER.'?p=%pg'); 
	}

	function acc_mode(){ return $this->mode; }
	function acc_max_lien(){ return $this->max_lien; }
	function acc_nb_page(){ return $this->nb_page; }
	function acc_num_page(){ return $this->num_page; }
	function acc_url(){ return $this->url; }
	function acc_avec_p(){ return $this->avec_p; }

	function mut_avec_p($a){ $this->avec_p = (bool)$a; }
	function mut_max_lien($nb){ $this->max_lien = absint($nb); }
	function mut_mode($m)
	{
		if( in_array($m, array(self::NORMAL, self::COUPE) ) )
		{
			$this->mode = $m; 
		}
	}

	function mut_nb_page($nb)
	{
		$this->nb_page = absint($nb); 
	}

	function mut_num_page($p)
	{
		$this->num_page=$p; 
	}

	function mut_url($url)
	{
		if( strpos($url, '%pg' ) !== FALSE )
		{
			$this->url = $url; 
			return TRUE; 
		}
		return FALSE; 
	}
	
	function affiche()
	{
		if( $this->avec_p )
		{
			echo '<p class="pagination" >';
		}
		if( $this->mode == self::COUPE && $this->nb_page > 10)
		{
			for($i=0; $i<3; $i++)
				$this->aff_lien($i); 
			if( $i < abs($this->num_page-3) )
				echo '...';
			for( $i= $i>abs($this->num_page-3) ? $i : abs($this->num_page-3); $i<$this->num_page+4 && $i<$this->nb_page; $i++)
				$this->aff_lien($i); 
			if( $i < $this->nb_page-3)
				echo '...'; 
			for( $i= $i>$this->nb_page-3 ? $i : $this->nb_page-3; $i<$this->nb_page; $i++)
				$this->aff_lien($i); 

		}
		elseif( $this->nb_page > 1 )
		{
			for($i=0; $i<$this->nb_page && $i<$this->max_lien; $i++)
			{
				$this->aff_lien($i); 
			}
		}
		if( $this->avec_p )
		{
			echo '</p>'; 
		}
	}

	function aff_dyn()
	{
		echo $this->acc_dyn(); 
	}

	function acc_dyn()
	{
		$res = ''; 

		for($i=1; $i<=$this->nb_page && $i<$this->max_lien; $i++)
		{
			$res .= '<a data-num="'.$i.'" '.( ($this->num_page+1)==$i ? 'class="actif"' : '' ).' href="'.
				str_replace('%pg', $i, $this->url).'" >'.$i.'</a> '; 
		}

		return $res; 
	}

	private function aff_lien($i)
	{
		echo '<a class="'.($this->num_page ==$i ? 'actif' : 'inactif' ).'" href="'.
			str_replace('%pg', $i, $this->url).'" >'.($i+1).'</a> '; 
	}
}
