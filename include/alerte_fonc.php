<?php
function alerte_verifier($id)
{
	if( empty($id ) )
	{
		return FALSE; 
	}

	if( is_numeric($id ) )
	{
		$cond = ' = '.(int)$id; 
	}
	elseif( is_array($id) )
	{
		$cond = ' IN ( '.implode(',', array_map('intval', $id) ).')'; 
	}
	else
	{
		return FALSE; 
	}

	req('UPDATE alerte SET etat='.VERIFIER.' WHERE id '.$cond.' ');

	return TRUE; 
}
