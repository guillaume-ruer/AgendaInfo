<?php
// On est plus sensé avoir besoin de la connexion 
connexion_fin(); 

switch($PAT->acc_type() )
{
	case patron::HTML : 
		header('Content-type: text/html; charset=utf8'); 
		foreach( $PAT->acc_haut() as $PAT_fichier )
		{
			require $PAT_fichier; 
		}
		foreach( $PAT->acc_patron() as $PAT_fichier )
		{
			echo "<!-- Corps -->\n"; 
			echo '<div id="contenu" >'."\n"; 
			require $PAT_fichier;
			echo "<!-- Fin Corps -->\n"; 
			echo '</div>'."\n"; 
		}
		foreach( $PAT->acc_bas() as $PAT_fichier )
		{
			require $PAT_fichier; 
		}
	break; 
	case patron::XML : 
		header('Content-type: text/xml; charset=utf8'); 
		echo '<?xml version="1.0" encoding="utf8" ?>'."\n";
		echo "<racine>\n"; 
		foreach( $PAT->acc_patron() as $PAT_fichier)
		{
			require $PAT_fichier;
		}
		echo "</racine>\n"; 
	break; 
	case patron::RSS :
		header('Content-type: application/rss+xml'); 
		foreach($PAT->acc_haut() as $PAT_fichier)
		{
			require $PAT_fichier; 
		}
		foreach( $PAT->acc_patron() as $PAT_fichier)
		{
			require $PAT_fichier;
		}
		foreach( $PAT->acc_bas() as $PAT_fichier)
		{
			require $PAT_fichier; 
		}
	break; 
}
