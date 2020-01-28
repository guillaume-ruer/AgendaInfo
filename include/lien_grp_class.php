<?php
require_once C_INC.'lien_class.php'; 

class lien_grp extends id_nom
{
	private $nb=0; 
	private $ls=array(); 

	function mut_nb($nb){ $this->nb = (int)$nb ; }
	function acc_nb(){ return $this->nb; }

	
	function str2ls($str)
	{
		$tab = explode(';;', $str); 

		foreach($tab as $l )
		{
			$t = explode('_&_', $l);
			foreach( $t as $pair )
			{
				list( $cle, $val) = explode('=', $pair); 
				$tf[$cle]=$val; 
			}

			$this->ls[] = new lien( $tf ); 
		}
	}

	function acc_ls()
	{
		return $this->ls; 
	}
}
