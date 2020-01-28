<?php
exit();
include '../include/init.php'; 

$pre = $BDD->prepare('UPDATE Contact SET adherent=?, site=? WHERE id=? LIMIT 1 ');
$donne = req('SELECT * FROM Contact_pub ');

while($do = fetch($donne) )
{
	$deb = strpos($do['Texte'],',' )+1; 
	$fin = strpos($do['Texte'],':'); 
	
	preg_match('`(((?:http://)|(?:www\.)){1}([a-z/A-Z0-9_.-]+)) ?`', $do['Texte'], $match ); 
	$site = (isset($match[0]) ) ? $match[0] : '';

	$tab = array( 
		substr($do['Texte'], $deb, $fin - $deb ), 
		$site, 
		$do['Contact_id']
	); 
	
	if($do['Contact_id'] == 947 )
	imp($tab); 
	//$pre->execute( $tab ); 
}


?>
