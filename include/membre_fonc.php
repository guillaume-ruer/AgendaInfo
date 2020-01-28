<?php
function utilisateur_sup( $id )
{
	$id = absint($id); 
	req('DELETE FROM Utilisateurs WHERE id='.$id.' LIMIT 1');
	req('DELETE FROM structure_droit WHERE utilisateur='.$id.' LIMIT 1 '); 
}

