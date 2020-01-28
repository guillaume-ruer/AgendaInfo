<?php
require '../../include/init.php'; 
require C_INC."php-export-data-master/php-export-data.class.php";
include C_INC.'location_fonc.php'; 

function str2url($chaine)
{
        $chaine = utf8_encode(str_replace(str_split(utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ')),
                str_split('aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'), utf8_decode($chaine) ) );
        $chaine = strtolower($chaine); 
        $chaine = preg_replace('`[^a-z0-9]`', '-', $chaine); 
        $chaine = preg_replace('`(^|-)(le|l|au|du|la|a-la|de-la|un|une|les|aux|des)(-|$)`','-', $chaine); 
        $chaine = preg_replace('`-+`', '-', $chaine); 
        $chaine = trim($chaine, '-'); 
        return $chaine; 
}

//Récupération du code 
$code = ( isset( $_POST['idl'] ) ) ? (int)$_POST['idl'] : 0; 
$deb = ( isset( $_POST['deb'] ) ) ? date_format_traitement($_POST['deb']) : NULL; 
$fin = ( isset( $_POST['fin'] ) ) ? date_format_traitement($_POST['fin']) : NULL; 
$max = ( isset( $_POST['max'] ) ) ? (int)$_POST['max'] : 0; 

if(!empty($code) )
{
	$donne = req('SELECT * FROM Externe WHERE code='.(int)$code .' LIMIT 1 '); 

	if($do = fetch($donne) ) 
	{
		$id_externe = (int)$do['id'];
	}
	else
	{
		exit(); 
	}
}
else
{
	exit(); 	
}



//Traitement sur les dates 
$realdate = $datepast = ''; 
mes_date(date('Y-m-d') , 1200, $realdate, $datepast);

if( !empty($deb) )
{
	$realdate = $deb; 
}

// Evenements
$lsevent = new ls_evenement( array(
	'champ' => EVCH_DATE|EVCH_LIEU|EVCH_DESC|EVCH_CONTACT|EVCH_CAT|EVCH_TARIF|EVCH_TOUTE_DATE, 
	'fi_date_min' => $realdate,
	'fi_date_max' => !empty($fin) ? $fin : NULL,
	'fi_id_externe' => $id_externe,
	'fi_str_actif' => FALSE, 
	'order' => ls_evenement::ORDER_DATE_LIEU
) ); 

if( !empty($max) )
{
	$lsevent->mut_mode(reqo::LIMITE); 
	$lsevent->mut_nb_par_page($max); 
}
else
{
	$lsevent->mut_mode(reqo::NORMAL); 
}

$lsevent->requete(); 

$excel = new ExportDataExcel('browser');
$excel->filename = str2url($do['nom']).".xls";

$excel->initialize();
$excel->addRow(array('titre', 'description', 'contact titre', 'contact tel', 'contact site', 'contact structure nom', 'categorie', 'tarif', 'public', 'dates', 'lieux') ); 

while($ev = $lsevent->parcours() )
{
	$tmptab=array(); 
	foreach($ev->acc_tab_lieu() as $l ) 
	{
		$tmptab[] = $l->acc_nom(); 
	}

	$excel->addRow(array(
		$ev->acc_titre(),
		$ev->acc_desc(),
		$ev->acc_contact()->acc_titre(),
		$ev->acc_contact()->acc_tel(),
		$ev->acc_contact()->acc_site(),
		$ev->acc_contact()->acc_structure()->acc_nom(),
		$ev->acc_categorie()->acc_nom(), 
		$ev->acc_tarif()->acc_nom(),
		$ev->acc_public()->acc_nom(), 
		implode(',',$ev->acc_tab_date() ).' ', 
		implode(',', $tmptab ),
	));
}

$excel->finalize();

