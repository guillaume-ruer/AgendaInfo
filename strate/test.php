<?php
try
{
	$bdd = new PDO('mysql:host=mysql5-11;dbname=infolimodb2009', 'infolimodb2009', 'goi4VNSm');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}

$add = 'http://cours-glandeurs.alwaysdata.net/externe/bidon.php?c=45678';
echo '<h3>en utilisant <strong>include</strong> vers '.$add.'</h3>'; 
include $add; 


echo '<h3>en utilisant <strong>readfile</strong> vers '.$add.'</h3>'; 
readfile($add );

