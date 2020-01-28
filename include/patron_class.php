<?php

class patron
{
	private $type=self::HTML;

	private $mess=array(); 
	private $script=array();
	private $javascript=array(); 
	private $style=array(); 
	private $meta = array(); 
	private $link = array(); 
	private $titre=''; 

	private $haut=array();
	private $bas=array();
	private $patron = array(); 
	private $var = array(); 

	const HTML=0; 
	const XML=1; 
	const RSS=2; 

	function __construct()
	{
		$this->haut = array(C_PATRON.'haut.php'); 
		$this->bas = array(C_PATRON.'bas.php');
		$this->patron[] = 'patron/'.FICHIER.'_p.php'; 
		if( func_num_args() > 0 ) $this->mut_type( func_get_arg(1) ); 
	}

	/*
		Accesseurs 
	*/ 
	function acc_link(){ return $this->link; }
	function acc_meta(){ return $this->meta; } 

	function acc_haut(){ return $this->haut; } 

	function acc_type()
	{
		return $this->type; 
	}

	function acc_bas()
	{
		return $this->bas; 
	}

	function acc_patron()
	{
		return $this->patron; 
	}

	function acc_mess()
	{
		return $this->mess; 
	}

	function val($nom)
	{
		return isset($this->var[$nom]) ? $this->var[$nom] : ''; 
	}

	/*
		Mutateurs 
	*/ 

	function ajt_haut($patron, $dos = C_PATRON )
	{
		$this->haut[] = $dos.$patron; 
	}

	function def_haut()
	{
		$this->haut = array(); 
	}

	function ajt_bas($patron, $dos = C_PATRON )
	{
		$this->bas[] = $dos.$patron; 
	}

	function def_bas()
	{
		$this->bas = array(); 
	}

	function mvar($nom, $val )
	{
		$this->var[$nom] = $val; 
	}

	function mut_type($type)
	{
		if( in_array($type, array( self::HTML, self::XML, self::RSS ) ) )
		{
			$this->type = $type; 

			switch($type)
			{
				case self::HTML :
					$this->haut = array(C_PATRON.'haut.php'); 
					$this->bas = array(C_PATRON.'bas.php');
				break; 
				case self::RSS : 
					$this->haut = array(C_PATRON.'haut_rss.php'); 
					$this->bas = array(C_PATRON.'bas_rss.php');
				break; 
			}
		}
	}

	function mut_titre( $titre )
	{
		$this->titre = $titre; 
	}

	function ajt_mess($mess, $class=NULL )
	{
		$this->mess[] = is_null($class) ? $mess : array('class'=>$class, 'mess' => $mess ); 
	}

	function ajt_script($nom, $dos = C_JAVASCRIPT, $attr=array() )
	{
		$this->script[] = array($dos.$nom, $attr); 
	}

	function def_script()
	{
		$this->script = []; 
	}

	function ajt_style($nom, $dos = C_STYLE )
	{
		$this->style[] = $dos.$nom; 
	}

	function def_style()
	{
		$this->style = array(); 
	}

	function ajt_meta($nom, $contenu)
	{
		$this->meta[] = array($nom, $contenu); 
	}

	function ajt_link($attr )
	{
		$this->link[] = $attr; 
	}

	function def_patron()
	{
		$this->patron = array(); 
	}

	function ajt_patron($patron, $dos = C_PATRON )
	{
		$this->patron[] = $dos.$patron; 
	}

	/*
		Pour affichage 
	*/

	function titre()
	{
		return $this->titre; 
	}

	/*
		Affichage 
	*/

	function affiche_titre()
	{
		echo $this->titre; 
	}

	function affiche_mess()
	{
		switch($this->type)
		{
			case patron::XML :
			case patron::RSS : 
				foreach( $this->mess as $mess ) {
					echo '<message class="'.( is_array($mess) 
						? $mess['class'].'" >'.$mess['mess'] 
						: 'message" >'.$mess ) 
					."</message>\n"; 
				}
			break; 
			case patron::HTML : 
				foreach( $this->mess as $mess ) {
					echo '<p class="'.( is_array($mess) ? $mess['class'].'" >'.$mess['mess'] : 'message" >'.$mess ) ."</p>\n"; 
				}
			break;
		}
	}

	function affiche_script()
	{
		foreach( $this->script as $script ) 
		{
			echo '<script type="text/javascript" src="', $script[0],'"'; 
			foreach( $script[1] as $attr => $val ) 
			{
				echo ' '.$attr.'="'.$val.'"'; 
			}
			echo " ></script>\n"; 
		}
	}

	function affiche_style()
	{
		foreach( $this->style as $style )
		{
			echo '<link rel="stylesheet" type="text/css" href="',$style,"\" />\n"; 
		}
	}

	function affiche_meta()
	{
		foreach( $this->meta as $do )
		{
			echo '<meta name="'.secuhtml($do[0]).'" content="'.secuhtml($do[1])."\" />\n"; 
		}
	}

	function affiche_link()
	{
		foreach($this->link as $link )
		{
			echo '<link '; 
			foreach($link as $n => $v )
			{
				echo $n.'="'.$v.'" '; 
			}
			echo "/>\n";
		}
	}

	function ajt_javascript($var)
	{
		$this->javascript[] = $var;
	} 

	function aff_javascript()
	{
		foreach($this->javascript as $js )
		{
			echo $js."\n"; 
		}
	}
}
