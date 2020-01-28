<?php

function trace_affiche_lien($conf) 
{
	echo '<ul>'; 
	foreach($conf as $id => $type )
	{
		echo '<li><a href="trace.php?type='.$id.'" >'.$type['nom'].'</a></li>'; 
	}
	echo '</ul>'; 
}

function trace_secu($var)
{
	return strip_tags($var, '<a><p><br><strong><em>'); 
}

function trace_nom_type($type, $conf)
{
	return $conf[ $type ]['nom']; 
}

