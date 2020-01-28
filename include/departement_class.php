<?php

class departement extends id_nom
{
	private $num=0;
	public static $TAB_DEP = array(87 => 'Haute-Vienne', 19 => 'Corrèze' , 23 => 'Creuse' ); 

	function acc_num(){ return $this->num; } 
	function acc_nom()
	{ 
		if( isset(self::$TAB_DEP[$this->num]) )
		{
			return self::$TAB_DEP[$this->num]; 
		}
	}

	function mut_num($n)
	{ 
		$this->mut_id($n); 
		$this->num = absint($n); 
	} 

}

$donne = req('SELECT * FROM Lieu_grp WHERE ordre=1'); 

departement::$TAB_DEP = []; 

while($do = fetch($donne) )
{
	departement::$TAB_DEP[ $do['num'] ] = $do['Nom']; 
}
