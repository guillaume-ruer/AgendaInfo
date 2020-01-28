<?php
include '../../include/init.php'; 

non_autorise(PREFIX); 

include C_INC.'reqa_class.php'; 


if(isset($_GET['sup'] ) )
{
	req('DELETE FROM prefixe_event WHERE id='.absint($_GET['sup']).' LIMIT 1 ');
	mess('Préfixe supprimé.'); 
}

$prefixe = new reqa('
	SELECT prefixe, id 
	FROM prefixe_event 
	ORDER BY prefixe
'); 

include PATRON;
