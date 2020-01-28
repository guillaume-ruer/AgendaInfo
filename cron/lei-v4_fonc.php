<?php

function my_mktime($date)
{
        $tab_au = explode('-', $date);
	return mktime(0,0,0, $tab_au[1], $tab_au[2], $tab_au[0]);
}

function date_duau_elim($date_duau, $ecart_ignore )
{       
        foreach($date_duau as $duau )
        {
                $tdu = my_mktime($duau['du']);
                $tau = my_mktime($duau['au']);
                                
                if($tau < $tdu )
                {
                        return TRUE;
                }
                                
                if( ( $tau - $tdu)> $ecart_ignore )
                {               
                        return TRUE;    
                }                       
        }                       

        return FALSE;           
}      

function est_visite_guide($type_nom)
{
        return preg_match('`visites? guidé`i', $type_nom );
}
	
function dans_limousin($cp)
{       
        return preg_match('`^(19|87|23)`', $cp);
}      

function date_duau($node)
{       
        $horaire = $node->getElementsByTagName('Horaire');
        $date_duau=array();
                
        foreach($horaire as $h )
        {
                $du = date_lei2infolimo(contenu_element('DATE_DEBUT', $h) );
                $au = date_lei2infolimo(contenu_element('DATE_FIN', $h) );
                $date_duau[] = array( 'du' => $du, 'au' => $au);
        }

        return $date_duau;
}

function date_lei2infolimo($date)
{
        return preg_replace('`([0-9]+)/([0-9]+)/([0-9]+)`', '$3-$2-$1', $date );
}          

function contenu_element($nom, $node)
{               
        $ret = '';
        if($balise = $node->getElementsByTagName($nom)->item(0) )
        {
                $ret = trim($balise->nodeValue) ;
        }
        
        return $ret;
}

function prcent_majuscule($str )
{
	if( empty($str) )
	{
		return 0; 
	}

	$nbc = strlen($str); 
	$tab = count_chars($str, 1); 
	$nbm = 0; 

	for($i=ord('A'); $i<=ord('Z'); $i++ )
	{
		if(isset($tab[$i]) )
		{
			$nbm += $tab[$i]; 
		}
	}
	
	return ceil(100*$nbm / $nbc); 
}

$TAB_COMMENTAIRE=''; 

function vide_tc()
{
	global $TAB_COMMENTAIRE; 
	$TAB_COMMENTAIRE = ''; 
}

function ajt_tc($mess)
{
	global $TAB_COMMENTAIRE; 
	$TAB_COMMENTAIRE.= $mess."\n"; 
}

function acc_tc()
{
	global $TAB_COMMENTAIRE; 
	return $TAB_COMMENTAIRE; 
}

/*
	param : le nœud de l'evenement 
	retour : le nom de l'adhérent
*/
function prod_nom($node)
{

	$nom = contenu_element('NOM_PERSONNE_EN_CHARGE', $node); 
	if(empty($nom) )
	{
		$nom = contenu_element('NOM_RESPONSABLE', $node); 
	}

	return $nom; 
}

/*
	Info contact 
*/

function prod_prenom($node)
{

	$prenom = contenu_element('PRENOM_PERSONNE_EN_CHARGE', $node);
	if(empty($prenom) )
	{
		$prenom = contenu_element('PRENOM_RESPONSABLE', $node); 
	}

	return $prenom; 
}

function prod_site($node)
{
	$site = contenu_element('ADRPEC_URL', $node); 
	if( empty($site) )
	{
		$site = contenu_element('ADRPREST_URL', $node); 

		if( empty($site ) )
		{
			$site = contenu_element('ADRPROD_URL', $node); 
		}
	}

	return $site; 
}

function prod_tel($node)
{
	$tel = contenu_element('ADRPEC_TEL', $node); 
	if(empty($tel ) )
	{
		$tel = contenu_element('ADRPROD_TEL', $node); 
	}

	return $tel; 
}

function recherche_contact($id_contact_lei, $source=evenement::LEI)
{
	static $rech_contact=NULL; 

	if(is_null($rech_contact) )
	{
		$rec_contact = array(); 
		$donne = req('SELECT id_lei idl, id_infolimo AS idc FROM contact_lei WHERE source='.(int)$source.' '); 
		while($do = fetch($donne ) )
		{
			$rech_contact[ $do['idl'] ] = $do['idc']; 
		}
	}

	return isset($rech_contact[ $id_contact_lei ]) ? $rech_contact[ $id_contact_lei ] : FALSE; 
}

function init_lei($idlei)
{
	static $meta = NULL;

	if( is_null($meta ) )
	{
		$meta = prereq('
			SELECT e.id, e.lei id_externe, hl.h_com, hl.h_lieu, hl.h_titre, 
				hl.h_theme, hl.h_contact, hl.der_verif
			FROM Evenement e
			JOIN hash_lei hl
				ON e.id = hl.idevent
			WHERE e.lei=?
			LIMIT 1 
		'); 
	}

	exereq($meta, array($idlei) );
	$do = fetch($meta);
	if( $do  )
	{
		$ev = new evenement_lei($do); 
		return $ev; 
	}
	else
	{
		return FALSE; 
	}

}

function select_duau($id)
{
	static $sel=NULL;

	if( is_null($sel) )
	{
		$sel = prereq('
			SELECT datedu, dateau FROM date_lei 
			WHERE idevent=? 
			AND dateau>=\''.date('Y-m-d').'\' 
			ORDER BY datedu 
		'); 
	}
	
	exereq($sel, array($id) ); 
	$tab=array();

	while($do = fetch($sel) )
	{
		$tab[] = array('du' => $do['datedu'], 'au' => $do['dateau']);
	}

	return $tab; 
}

function hash_duau($tab_duau)
{
	$chaine ='';
	foreach($tab_duau as $duau )
	{
		$chaine .= $duau['du'].$duau['au'];
	}

	return hashfct($chaine); 
}

function id_lieu($nom_lieu, $cp) 
{
	static $lieu=NULL, $tab_lieu=array(); 

	if(is_null($lieu) )
	{
		$lieu = prereq('SELECT Lieu_ID AS id, Lieu_Dep dep FROM Lieu WHERE Lieu_Ville LIKE ? ');
	}

	// Buffer pour évité trop de requête. 
	if( $id = array_search($nom_lieu, $tab_lieu ) )
	{
		return $id; 
	}

	//Transformation pour trouvé le nom dans la base d'info-limousin
	$r_lieu = str_replace(' ', '-', $nom_lieu);
	$r_lieu = str_ireplace( array('Saint', 'saints', 'st', 'ste' ), 's%', $r_lieu);
	$r_lieu = str_ireplace( array('l\'', 'd\'', 's/' ), '%', $r_lieu);

	exereq($lieu, array( $r_lieu ) );

	if($lieu->rowCount() == 1)
	{
		//On a un seul résultat alors on l'ajoute
		$do = fetch($lieu);
		$tab_lieu[ (int)$do['id'] ] = $nom_lieu; 
		return (int)$do['id'];
	}
	elseif($lieu->rowCount() > 1 )
	{
		while($do = fetch($lieu) )
		{
			if( (int)($cp/1000) == $do['dep'])
			{
				return (int)$do['id'];
			}
		}
	}

	return FALSE; 
}

function id_theme_auto($source=evenement::LEI)
{
	static $tab_id=NULL;

	if( is_null($tab_id) )
	{
		$tab_id = array(); 
		$donne = req('SELECT id_theme FROM theme_lei WHERE source='.(int)$source.' AND auto_actif=1 ');
		while($do = fetch($donne) )
		{
			$tab_id[] = (int)$do['id_theme']; 
		}
	}

	return $tab_id; 
}

function id_theme($nom_theme, $source=evenement::LEI)
{
	static $tab_theme=NULL, $tab_theme_id=NULL; 

	if(is_null($tab_theme) )
	{
		$theme= req('SELECT id_theme, nom_lei  FROM theme_lei WHERE source='.(int)$source.' '); 
		$tab_theme = array();
		$tab_theme_id = array(); 
		
		while( $do = fetch($theme) )
		{
			$tab_theme[] = $do['nom_lei']; 
			$tab_theme_id[] = (int)$do['id_theme']; 
		}
	}

	// Buffer pour éviter trop de requete 
	if( ($id = array_search($nom_theme, $tab_theme) ) !== FALSE )
	{
		return $tab_theme_id[ $id ]; 
	}
	else
	{
		return FALSE;
	}
}

function duau2date($tab_duau, &$ndate )
{
	$ndate = array(); 	
	$bon = TRUE; 

	foreach($tab_duau as $duau )
	{
		extract($duau);

		if($du == $au )
		{
			$ndate[] = $du; 
		}	
		else
		{
			$tdu = my_mktime($du);
			$tau = my_mktime($au); 

			$ecart = $tau - $tdu ; 

			if($ecart > ECART_PAS_AUTO )
			{
				$bon = FALSE; 
			}
			else
			{
				  for($i = $tdu; $i <= $tau; $i+=(24*3600 ) )
				  { 
					$ndate[] = date('Y-m-d', $i );
				  }
			}
		}
	}

	return $bon;
}

function duau2chaine($date)
{
	$chaine = ''; 
	foreach($date as $duau )
	{
		$chaine .= 'du '.$duau['du'].' au '.$duau['au']."\n"; 
	}
	return $chaine; 
}

function event_lei_enr($ev, $com, $id, $mode, $donne)
{
	event_enr($ev, $com, $id);

	if( $mode )
	{
		insert_hash($ev->acc_id(), $donne['com'], $donne['lieu'], $donne['titre'], $donne['categorie'], $donne['contact'] ); 
		insert_dateduau($ev->acc_id(), $donne['date']); 
	}
	else
	{
		maj_hash($ev->acc_id(), $donne['com'], $donne['lieu'], $donne['titre'], $donne['categorie'], $donne['contact'] ); 
	}
}

function insert_hash($id, $com, $lieu, $titre, $theme, $contact )
{
	static $ins=NULL;
	if(is_null($ins) )
	{
		$ins = prereq('INSERT INTO hash_lei(idevent, h_com, h_lieu, h_titre, h_theme, der_verif, h_contact ) VALUES(?,?,?,?,?,?,?) ');
	}

	exereq($ins,array($id, hashfct($com), hashfct($lieu), hashfct($titre), hashfct($theme), time(), hashfct($contact) ) );
}

function hashfct($var)
{
	return md5($var); 
}

function maj_hash($id, $com, $lieu, $titre, $theme, $contact )
{
	static $m=NULL;

	if(is_null($m) )
	{
		$m=prereq("
			UPDATE hash_lei 
			SET h_com=:com, h_lieu=:lieu, h_titre=:titre, h_theme=:theme, h_contact=:contact, der_verif=:time
			WHERE idevent=:id 
			LIMIT 1 ");
	}

	exereq($m, array(
		'id' => $id,
		'com' => hashfct($com),
		'lieu' => hashfct($lieu),
		'titre' => hashfct($titre), 
		'theme' => hashfct($theme), 
		'contact' => hashfct($contact),
		'time' => time()
	) ); 
}

function maj_der_verif($id )
{
	static $maj_der_verif; 

	if(is_null($maj_der_verif) )
	{
		$maj_der_verif = prereq('UPDATE hash_lei SET der_verif=? WHERE idevent=? LIMIT 1 '); 
	}
	exereq( $maj_der_verif, array(time(), $id ) ); 	
}

function alerte($ide, $cause, $etat, $type )
{
	static $pre=NULL;
	if(is_null($pre) )
	{
		$pre = prereq('INSERT INTO alerte(idevent, cause, etat, time, type) VALUES(?,?,?,?,?) ');
	}

	exereq($pre, array( $ide, $cause, $etat,time(), $type ) );
}

function insert_dateduau($id, $date_duau )
{
	static $ins=NULL, $sup;
	if(is_null($ins) )
	{
		$sup = prereq('DELETE FROM date_lei WHERE idevent=?'); 
		$ins = prereq('INSERT INTO date_lei(idevent, datedu, dateau )VALUES(?,?,?) '); 
	}

	exereq($sup, array($id) );

	foreach($date_duau as $duau )
	{
		exereq($ins, array($id, $duau['du'], $duau['au'] ) ); 
	}
}

function supp_lei($source=evenement::LEI)
{
	$sup = prereq('UPDATE Evenement SET Actif=2 WHERE id=? LIMIT 1 ');

	$donne = req("SELECT hl.idevent, hl.der_verif + (3850*24) AS dv
		FROM hash_lei hl
		LEFT JOIN Evenement_dates ed 
			ON hl.idevent = ed.Evenement_id 
		LEFT JOIN Evenement e 
			ON e.id = hl.idevent 
		WHERE (Evenement_date BETWEEN '".date('Y-m-d')."' AND '".date('Y-m-d', time()+(88*24*3600) )."' )
		AND e.Actif!=2
		AND e.source=".$source."
		GROUP BY Evenement_id 
	"); 
	$nb=0;
	while($do = fetch($donne ) )
	{
		if( (int)$do['dv'] < time() )
		{
			$nb++;
			exereq($sup, array($do['idevent']) );
			alerte($do['idevent'], 'Suppresion LEI (Evenement à priori disparu du flux) ', NON_VERIFIER, ALERTE_LEI_SUPP );
		}
	}

	return $nb;
}

function stat_lei($nbverif, $nbins, $nbmaj, $nbelim, $nbsup, $req, $pre, $exe, $tps, $auto_actif, $source=evenement::LEI )
{
        $tab = array(0,0,0,0);
        $donne = req('SELECT Actif, COUNT(*) AS nb FROM Evenement WHERE source='.$source.' GROUP BY Actif ');
        while( $do = fetch($donne) )
        {   
                $tab[ (int)$do['Actif'] ] = (int)$do['nb'];
        }   

        req('INSERT INTO stat_lei ( time, supprime, actif, masque, nbverif, nbins, nbmaj, 
		nbelim, nbsup, nbreq, nbpre, nbexe, tps, auto_actif, source ) 
                VALUES ( '.time().' , '.$tab[2].', '.$tab[1].', '.$tab[0].", $nbverif, $nbins, $nbmaj, 
			$nbelim, $nbsup, $req, $pre, $exe, '$tps', $auto_actif, ".$source." ) ");
}
