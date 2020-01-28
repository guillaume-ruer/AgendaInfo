<?php
/*
	Système de cache.

	L'id est l'emplacement/nom du fichier de cache 
	Php doit avoir les droits pour écrire ce fichier

	function : 
		- cache(); 
			Lance le cache / Sort le cache 
		- cache_valide();
			TRUE -> cache à régénéré  
			FALSE -> Le cache est valide et n'a pas besoin d'être régénré
		- cache_id(); 
			 Donne un id de cache courant unique (un numéro dans le nom
			 s'incrémente à chaque appelle -> nb_cache ): 
			 CHEMIN/nom_du_fichier_courant-LANGUE-nb_cache  

	s'utilise comme ça : 

	$id = cache_id();

	if(cache_valide($id ) )
	{
		//block à éxécuter ou non en fonction du cache
		//--Typiquement : création d'un tableau avec requete sql
			
	}
		//Du code à interpréter tout le temps. 
	if(cache($id) )
	{
		//block à éxécuter ou non en fonction du cache
		//-- Typiquement : Generation du code html en php

	}
	cache();

	Pour avoir du code dynamique dans le cache, il suffit d'y inséré 
	une chaine de caractère correspondant à un code php 

	ex : 

	echo '<?php echo rand(1, 89 ); ?>'; 

	ATTENTION toute fois à bien vérifier que ce code soit valide. 
*/

define('C_CACHE', RETOUR.'dos-php/cache/');
define('CACHE_TIME', time() );

function cache()
{
	static $deb=TRUE, $idc = '', $recup=FALSE;

	if($deb)
	{
		$recup = FALSE;

		if(func_num_args() < 1)
		{
			return FALSE;	
		}

		$idc= C_CACHE.func_get_arg(0);
		$deb = FALSE;
		
		$temps_cache = (func_num_args() >= 2 )? func_get_arg(1) : 3600 ; 

		if(file_exists($idc) AND filemtime($idc) > CACHE_TIME-$temps_cache )
		{
			return FALSE;
		}

		ob_start();
		$recup=TRUE;

		return TRUE;
	}
	else
	{
		if($recup)
		{
			$cache = ob_get_contents();
			ob_end_clean();
			file_put_contents( $idc, $cache);
		}

		include $idc;

		$deb = TRUE;
		$idc = '';
	}
}

function cache_valide( $id )
{
	$temps_cache = (func_num_args() >= 2 ) ? func_get_arg(1) : 3600;

	$idc = C_CACHE.$id;

	if(file_exists($idc) AND filemtime($idc) > CACHE_TIME-$temps_cache )
	{
		return FALSE;
	}

	return TRUE;
}

function cache_id()
{
	static $nb_cache=0;
	$nb_cache++;
	
	$nom = str_replace('.', '', basename($_SERVER['SCRIPT_NAME'], '.php') );
	$fichier = $nom.'-'.ID_LANGUE.'-'.$nb_cache;
	
	$args = func_get_args();
	foreach($args as $ele )
	{
		$fichier .= '-'.(string)$ele;
	}
	
	return $fichier;
}
