<?php
define('DOS_MEMOR', C_DOS_PHP.'memor/'); 

/*
	Donne la donnée enregistré
*/
function rappel($nom, $def='')
{
	return (file_exists(DOS_MEMOR.$nom ) ) ? unserialize( file_get_contents(DOS_MEMOR.$nom) ) : $def; 
}

/*
	Param : 
	- Nom du fichier qui gardera en mémoire 
	- Valeur (sera sérializé )
*/
function memor($nom, $val)
{
	if(!file_exists(DOS_MEMOR) )
	{
		if( ! mkdir(DOS_MEMOR) )
		{
			echo 'Erreur pour la mémorisation de donnée'; 	
		}
	}

	file_put_contents(DOS_MEMOR.$nom, serialize($val) ); 
}

/*
	Verrou
*/
function lock($nom, $time )
{
        if( rappel($nom ) > time() )
        {   
                exit('Ce n\'est pas encore l\'heure...'); 
        }   

        memor($nom, time()+$time);  
}


