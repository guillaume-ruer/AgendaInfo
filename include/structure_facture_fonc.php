<?php
define('NB_FICHIER_DOSSIER', 200); 

function structure_facture_dossier()
{
    $dos = C_DOS_PHP.'facture'; 

    if( !file_exists($dos) )
    {   
        mkdir($dos); 
        file_put_contents($dos.'/.htaccess', 'deny from all'); 
    }   

    $num = (int)rappel('facture-num'); 
    $dosf = $dos.'/f'.$num; 

    $nb = count( glob($dosf.'/*') );  

    if( $nb> NB_FICHIER_DOSSIER)
    {   
        $num++; 
        memor('facture-num', $num); 
        $dosf = $dos.'/f'.$num; 
    }   

    if( !file_exists($dosf) )
    {   
        mkdir($dosf); 
    }   

	return $dosf; 
}
