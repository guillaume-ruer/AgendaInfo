<?php
require_once RETOUR.'cron/lei-v4_fonc.php'; 

function aff_tab($tab, $p=0)
{
	$t = str_repeat("\t", $p); 
	$txt = ''; 

	foreach($tab as $c => $v)
	{
		if( is_array($v) )
		{
			$txt .= $t.$c."\n"; 
			$txt .= aff_tab($v, $p+1); 
		}
		else
		{
			$txt .= $t.$c."\t".'"'.$v.'"'."\n"; 
		}
	}

	return $txt; 
}

function ajt_unique(&$tab, $donne )
{
	if( !in_array($donne, $tab) )
	{
		$tab[] = $donne; 
	}

	return $tab; 
}

function ajt_unique_tab(&$tab, $donne )
{
	$tmp = explode('#', $donne); 

	foreach($tmp as $t )
	{
		if( !in_array($t, $tab) )
		{
			$tab[] = $t; 
		}
	}

	return $tab; 
}

function do_event($id, $cle)
{
	$url = 'http://wcf.tourinsoft.com/Syndication/3.0/aquitaine/'.$cle.'/Objects(\''.$id.'\')?$format=json';
	$data = file_get_contents($url); 
	$do = JSON_decode($data, TRUE); 
	return $do; 
}

function stq_tab_fam($refresh=FALSE, $cle_fam)
{
	$url = 'http://wcf.tourinsoft.com/Syndication/3.0/aquitaine/'.$cle_fam.'/Objects?$format=json&$expand=Structure'; 
	$dos = 'fichier/'; 
	$fichier = 'famtout.txt'; 

	if($refresh)
	{
		$json = file_get_contents($url); 
		file_put_contents($dos.$fichier, $json); 
	}
	else
	{
		$json = file_get_contents($dos.$fichier); 
	}

	$data = JSON_decode($json, TRUE); 
	return $data; 
}

/*
id SyndicObjectID
titre NOMOFFRE
com DESCRIPTIFOT 
combdd DESCRIPTIFOT
com1 DESCRIPTIFOT
com2 
lieu COMMUNE
cp CP  
categorie CATFMA
contact Structure Name

Champ de l'evenement
adrprod_tel Structure Phone
adrprod_url Structure Url 
adrprod_compl_adresse Structure (champ Adresse 1 à 3 concat)
adrpec_compl_adresse ORGAD 1 à 3, ORGADSUITE concat

date convertire DATES au format local

Donnée Contact (Str)
ct_nom 
ct_prenom
ct_tel 
ct_site
*/
function stq_conv_event($event)
{
	$do = []; 
	$str = $event['Structure']; 

	$dateheure = stq_conv_date_complet($event['DATESCOMPLET']); 

	$do['date'] = $dateheure['date_duau']; 

	$desc = stq_com($event, $dateheure['text']); 

	$do['id'] = $event['SyndicObjectID']; 
	$do['titre'] = trim($event['NOMOFFRE']); 
	$do['com'] = $desc; 
	$do['combdd'] = $desc; 
	$do['tarif'] = stq_tarif($event['TARIFSTEXTE'], $event['TARIFS']); 
	$do['lieu'] = $event['COMMUNE']; 
	$do['cp'] = $event['CP']; 

	$do['categorie'] = stq_categorie($event['CATFMA']); 
	$do['categorie_stq'] = $event['CATFMA']; 

	$do['date_maj'] =stq_date_maj($event['Updated']); 


	$do['contact'] = $str['SyndicStructureId']; 
	$do['ct_str_nom'] = $str['Name']; 
	$do['ct_adresse'] = trim($str['Address1'].' '.$str['Address2'].' '.$str['Address3']); 
	$do['ct_nom'] = ''; 
	$do['ct_prenom'] = ''; 
	$do['ct_tel'] = $str['Phone']; 
	$do['ct_site'] = $str['Url']; 

	return $do; 
}

function stq_date_maj($d)
{
	list($date, $time) = explode('T', $d);
	list($y, $m, $d )= explode('-', $date); 
	list($h, $min, $s) = explode(':', $time); 
	$r = mktime($h, $min, $s, $m, $d, $y); 
	return $r; 
}

function stq_categorie($cat)
{
	$tab = explode('#', $cat);
	return $tab[0]; 
}

function stq_implode_nonvide($tab)
{
	$tmp = []; 
	foreach($tab as $e )
	{
		if( !empty($e) )
		{
			$tmp[] = trim($e); 
		}
	}

	return implode(', ', $tmp); 
}

function stq_tel($tel, $telmob)
{
	$tab_tel = []; 
	$tab_telmob = []; 

	if( !empty($tel) )
	{
		$tab_tel = explode('#', $tel); 
	}

	if( !empty($telmob) )
	{
		$tab_telmob = explode('#', $telmob); 
	}

	return implode(', ', array_merge($tab_tel, $tab_telmob) ); 
}

function stq_conv_date($date)
{
	$tab_date = explode('#', $date); 
	$date_duau=array();

	foreach($tab_date as $date)
	{
		list($deb, $fin) = explode('|', $date); 
		$du = date_lei2infolimo($deb);
		$au = date_lei2infolimo($fin);
		$date_duau[] = array( 'du' => $du, 'au' => $au);
	}

	return $date_duau;
}

/*
	Dates de début et de fin de la manifestation (au format jj/mm/aaaa), 
	heure d'ouverture et de fermeture 1, 
	heure d'ouverture et de fermeture 2, 
	jours de fermeture séparés par |, chaque ligne étant séparée par # 
*/
function stq_conv_date_complet($date)
{
	$tab_dates = explode('#', $date); 
	$date_duau=array();

	$groupe_date = []; 

	foreach($tab_dates as  $d )
	{
		list($deb, $fin, $hdeb1, $hfin1, $hdeb2, $hfin2, $dferm ) =
			explode('|', $d); 

		$du = date_lei2infolimo($deb);
		$au = date_lei2infolimo($fin);
		$date_duau[] = array( 'du' => $du, 'au' => $au);
		
		if( !empty($hdeb1) || !empty($hfin1) )
		{
			$groupe_date[$deb.'_'.$fin][] = ['deb' => $hdeb1, 'fin' => $hfin1]; 
		}

		if( !empty($hdeb2) || !empty($hdeb2) )
		{
			$groupe_date[$deb.'_'.$fin][] = ['deb' => $hdeb2, 'fin' => $hfin2]; 
		}
	}

	$groupe_horaire = []; 

	foreach($groupe_date as $date => $horaire )
	{
		$tmp = []; 

		foreach($horaire as $h )
		{
			$tmp[] = stq_format_heure($h['deb']).'|'.stq_format_heure($h['fin']); 
		}

		$groupe_horaire[ implode('_', $tmp) ][] = $date; 
	}

	$text = ''; 
	if( count($groupe_horaire) == 1 )
	{
		reset($groupe_horaire); 
		$id = key($groupe_horaire); 
		$tabh = explode('_', $id); 
		foreach($tabh as $h )
		{
			list($deb, $fin) = explode('|', $h); 

			if( !empty($deb) && !empty($fin) )
			{
				$th[] = 'de '.$deb.' à '.$fin;
			}
			elseif( !empty($deb) )
			{
				$th[] = 'à partir de '.$deb;
			}
		}

		$text = implode(', ', $th).'.'; 
	}

	return ['date_duau' => $date_duau, 'groupe_horaire' => $groupe_horaire, 'text' => $text]; 
}

function stq_format_heure($heure)
{
	if( empty($heure) )
	{
		return ''; 
	}

	list($h, $m) = explode(':', $heure); 

	$h = ltrim($h, '0'); 

	if( $m == '00' )
	{
		$r = $h.'h';
	}
	else
	{
		$r = $h.'h'.$m; 
	}

	return $r; 
}

function stq_jr_ferm($ferm)
{
	$sem = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']; 

	$tab = explode(' ', $ferm); 

	$res = []; 
	$ind = 0; 

	foreach($tab as $f )
	{
		if( in_array($f, $sem) )
		{
			$res[$ind] = $f; 	
			$ind++; 
		}
		else
		{
			$res[$ind-1] .= ' '.$f; 
		}
	}

	return $res; 
}

function stq_tariftout($tarif)
{
	$tab_tarif = explode('#', $tarif); 
	$res = []; 

	foreach($tab_tarif as $tar )
	{
		list($intitule, $min, $max, $compl) = explode('|', $tar); 
		$var = ''; 

		if( strtolower($intitule) == 'gratuit')
		{
			$var = 'Gratuit'; 

		}
		elseif( !empty($min) || !empty($max) )
		{
			$var = $intitule.' '; 

			if( $min == $max )
			{
				$var .= $min.'€'; 
			}
			elseif( !empty($min) && !empty($max) )
			{
				$var .= 'de '.$min.'€ à '.$max.'€'; 
			}
			elseif( !empty($min) )
			{
				$var .= 'à partir de '.$min.'€'; 
			}
			elseif( !empty($max) )
			{
				$var .= $max.'€'; 
			}

		}

		if( !empty($var) )
		{
			if( !empty($compl) && !in_array($compl, ['0€']) )
			{
				$var .= ' ('.$compl.')'; 
			}

			$res[] = $var; 
		}
	}

	if( !empty($res) )
	{
		$res = 'Tarif(s) : '.implode(' ; ', $res); 
	}
	else
	{
		$res = ''; 
	}

	return $res; 
}

function stq_tarif($tariftexte, $tarif )
{
	if( !empty($tariftexte) )
	{
		return $tariftexte; 
	}
	elseif( !empty($tarif) )
	{
		return stq_tariftout($tarif); 
	}
}

function stq_com($event, $horaire)
{
	$desc = $event['DESCRIPTIFOT']; 

	$desc_ajt = ''; 
	$desc_deb = ''; 
	
	$tel = stq_tel($event['TEL'], $event['TELMOB']);

	if( !empty($tel) )
	{
		$desc_ajt .= ' Tél. : '.$tel.'.'; 
	}

	$url = implode(', ', explode('#', $event['URL']) ); 

	if(!empty($url) )
	{
		$desc_ajt .= ' Site : '.$url.'.';
	}
	
	$adrprod_compl_adresse = stq_implode_nonvide([$event['AD1'], $event['AD2'] ]); 

	if(!empty($adrprod_compl_adresse) )
	{
		$desc_deb = $adrprod_compl_adresse.'. ';
	}

	$tarif = stq_tarif($event['TARIFSTEXTE'], $event['TARIFS']);  
	if( !empty($tarif) )
	{
		$tarif = ' '.$tarif;
	}

	if( !empty($horaire) )
	{
		$horaire = $horaire.' '; 
	}

	return auto_format($horaire.$desc_deb.$desc.$tarif).$desc_ajt; 
}
