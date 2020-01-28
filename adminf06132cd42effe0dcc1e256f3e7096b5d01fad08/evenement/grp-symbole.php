<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'grp_symbole_class.php'; 


if(isset($_GET['id']) && droit(GERER_SYMBOLE) )
{
	grp_symbole::sup($_GET['id']); 
}

$lsgroupe = ls_grp_symbole(); 

include PATRON;
