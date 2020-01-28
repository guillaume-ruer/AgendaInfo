<?php

function article_const($conf)
{
	foreach($conf as $id =>$c )
	{
		if(!defined($c['const']) )
		{
			define($c['const'], $id ); 
		}
		else
		{
			imp('Nom de constante déjà utilisé : '.$c['const'].'.'); 
		}
	}
}

function article_type($conf, $id)
{
	return $conf[$id]['nom']; 
}

function article_droit_ajt($conf, $type )
{
	return (isset($conf[$type]['droit']['ajt']) ) ? $conf[$type]['droit']['ajt'] : 0; 
}

function article_droit_mod($conf, $type)
{
	return (isset($conf[$type]['droit']['mod']) ) ? $conf[$type]['droit']['mod'] : 0; 
}

function article_verif_droit($valeur_droit, $membre_droit )
{
	return empty($valeur_droit) || $valeur_droit & $membre_droit; 
}

function article_droit_etat($conf, $type, $etat )
{
	return isset($conf[$type]['droit']['etat'][$etat]) ? $conf[$type]['droit']['etat'][$etat] : 0; 
}

function article_membre_droit($conf, $type, $id_createur, $id_membre, $droit_membre )
{
	return (!empty($id_membre) && $id_createur == $id_membre) || $conf[$type]['droit']['mod'] & $droit_membre;
}

function article_com_droit($conf, $id_com_utilisateur, $membre, $type )
{
	return $id_com_utilisateur == $membre->id || $conf[$type]['droit']['com'] & $membre->droit; 
}

function article_affiche($ARTICLE_CONF,  $lsa)
{
	global $MEMBRE; 
	include C_ADMIN.'article/patron/ls-article.p.php'; 
}
