<?php

/*
	Vérification que ça fait plus de 24h qu'on a pas vider le cache
*/

define('C_DOS_PHP', '../dos-php/');
include '../include/fonc_memor.php'; 
$var_time = 'cron_cache_time'; 
$time = rappel($var_time ); 

if( ($time + (3500 * 24) ) < time() )
{
	/*
		Suppression de tout les fichier dans le dossier de cache qui ne commence pas par un point 
	*/
	$dir = '../dos-php/cache/';
	
	if (is_dir($dir)) 
	{
		if ($dh = opendir($dir)) 
		{
			while ( ($file = readdir($dh) ) !== false) 
			{
				if(!preg_match('`^\.(.*)$`', $file ) )
				{
					unlink($dir.$file ); 
				}
			}

			closedir($dh);
		}
	}
	
	//Mise en mémoir de la derniere suppresion du cache
	memor($var_time, time() ); 
	echo '<p>Le cache à été vidé</p>';
}
else
{
	echo '<p>Trop top</p>';
}
