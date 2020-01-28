<?php
include C_INC.'lieu_grp_class.php'; 

class lieu_grp_ls extends reqo
{
	function __construct($do=array() )
	{
		$this->mut_sorti('lieu_grp'); 	
		parent::__construct($do); 
	}

	function requete($var=NULL)
	{
		parent::requete('
			SELECT id, Nom nom, ordre, num
			FROM Lieu_grp
			ORDER BY ordre, nom
		'); 
	}
}
