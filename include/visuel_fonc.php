<?php

function visuel_const($conf)
{
	foreach($conf as $id => $do )
	{
		if( isset($do['const']) )
		{
			define($do['const'], $id); 
		}
	}
}

function visuel_type2id($conf, $type)
{
	foreach( $conf as $id => $do )
	{
		if( $do['type'] == $type )
		{
			return $id;
		}
	}

	return FALSE; 
}

function str_droit($ids)
{
	static $r = NULL;
	global $MEMBRE; 

	if(is_null($r) )
	{
		$r = prereq('SELECT * FROM structure_droit WHERE structure=? AND utilisateur=? LIMIT 1 '); 
	}

	if($MEMBRE->id_structure == $ids )
	{
		return TRUE ;
	}
	
	exereq($r, array($ids, ID) ); 

	if( $do = fetch($r) )
	{
		return $do['droit'] & STR_MODIFIER; 
	}
	else
	{
		return FALSE; 
	}
}

function sup_visuel($id)
{
	req('DELETE FROM Bandeaux WHERE id='.absint($id).' LIMIT 1 ');
}

define('VISUEL_DESCRIPTION_LIMITE', 250 ); 
define('MASQUE_URL', "`(https?://|www\.)[a-z0-9_-]+\.[a-z0-9,+.?/=&;_-]*`i" ); 

function visuel_description($visuel, $limite=FALSE )
{
	$nbcar_entete = strlen($visuel->titre); 
	$nbcar_texte = strlen($visuel->texte); 

	$entete = '<strong>'.$visuel->titre.'</strong> ';
	$visuel->texte = $visuel->texte; 

	if( !empty($visuel->id_contact) )
	{
		$nbcar_entete += strlen(' Contact : '. $visuel->c_titre. ' ' . $visuel->c_tel.' '.$visuel->c_site.' '); 
		$entete .= ' Contact : '.$visuel->c_titre.' '.$visuel->c_tel.' <a href="'.lien($visuel->c_site).'" >'.$visuel->c_site.'</a>';
	}

	if( $nbcar_entete + $nbcar_texte > VISUEL_DESCRIPTION_LIMITE)
	{
		// Si le texte dépasse : 
		$lien_coupe = FALSE; 

		if( preg_match_all( MASQUE_URL , $visuel->texte, $trouve, PREG_OFFSET_CAPTURE) )     
		{ 
			// Si y'a un lien dans le texte 
			foreach( $trouve[0] as $dolien )
			{
				list ( $url , $offset ) = $dolien; 
				$deb = $nbcar_entete + $offset; 
				$fin = $deb + strlen($url);

				if( $deb < VISUEL_DESCRIPTION_LIMITE && $fin > VISUEL_DESCRIPTION_LIMITE )
				{
					// Si le lien mord la limite 
					$lien_coupe = TRUE; 
					$l = mysubstr($url, 0, VISUEL_DESCRIPTION_LIMITE-$deb ); 
					$balise = '<a href="'.$url.'" >'.$l.'</a>'; 
					break; 
				}
				elseif( $deb > VISUEL_DESCRIPTION_LIMITE)
				{
					// Les autres liens dépasse de la limite 
					break; 
				}
			}
		}

		if( $lien_coupe )
		{
			// Un lien mord la limite 
			$texte = mysubstr($visuel->texte, 0, $offset ); // On vire à partir du lien 
			$texte = lien_text(secuhtml($texte) ); // Formatage des liens 
			$texte .= $balise; // Ajout de la balise crée 
		}
		else
		{
			// Aucun lien ne mord la limite 
			// Découre à la limite, puis formatage des liens 
			$texte = lien_text( secuhtml(mysubstr($visuel->texte, 0, VISUEL_DESCRIPTION_LIMITE - $nbcar_entete ) ) ); 
		}

		// Ajout du lien "lire la suite"
		$texte .= '... <a href="'.RETOUR.'page/affichettes'.$visuel->id.'.html" >Lire la suite</a>';
		return $entete.' '.$texte; 
	}
	else
	{
		// Le texte ne dépasse pas. 
		return $entete.' '.lien_text(secuhtml($visuel->texte) );
	}
}

function visuel_hasard($type)
{
	$donne = req('SELECT id, Image img, URL url FROM Bandeaux WHERE Type=\''.secubdd($type).'\' ORDER BY rand() LIMIT 1'); 

	if( $do = fetch($donne) )
	{
		return $do; 
	}
	else
	{
		return FALSE; 
	}
}
