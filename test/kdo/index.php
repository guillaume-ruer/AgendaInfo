<?php
include "mail.php"; 

function cadeau($offrant, $content )
{
	mel( 
		$offrant[1], 
		'A qui offrez vous un cadeau ?', 
		'Vous offrirez un cadeau à : '.$content[0].'.'
	); 
	
	echo "<p>mail envoyé : ".$offrant[0]." &lt;".$offrant[1]."&gt;</p>"; 
}

$tab = array(
	array('Thomas', 'strategeyti@gmail.com' ),
	array('André', 'andre_gosset@hotmail.com' ),
	array('Dominik', 'dominik.gosset@gmail.com' ),
	array('Patrik', 'paterpat@gmail.com'),
	array('Brice', 'gosset.brice@gmail.com'), 
); 


shuffle($tab); 

?>
<!DOCTYPE html>
<html>
<head>
	<title>Qui offira à qui</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta content="text/html" charset="utf8" />
</head>
<body>
	<h1>Qui offrira à qui ?</h1>
<?php 
for( $i=0, $f=count($tab)-1; $i<$f; $i++ )
{
	cadeau( $tab[$i], $tab[$i+1] ); 
}

cadeau($tab[$i], $tab[0]) 
?>

</body>
</html>
