<?php

function ls_grp_symbole()
{
	$lsgroupe = new reqa('SELECT secuhtml::nom_fr nom, absint::id FROM categories_grp ORDER BY nom_fr '); 
	return $lsgroupe; 
}
