<?php
require '../../include/init.php'; 

header('Content-type: text/csv'); 
header("Content-Disposition: attachment; filename=file.csv");

$donne = req('
	SELECT u.nom, u.prenom, s.email
	FROM structure s
	JOIN Utilisateurs u
		ON u.id_structure = s.id
	WHERE s.actif=1
	AND s.email != \'\' 
	AND (u.nom != \'\' OR u.prenom != \'\')
'); 

while($do = fetch($donne) )
{
	echo $do['email'].','.$do['nom'].','.$do['prenom']."\n"; 
}

