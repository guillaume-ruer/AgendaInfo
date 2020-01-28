<?php
require '../../include/init.php'; 
require C_INC."php-export-data-master/php-export-data.class.php";

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

http_param( array('date' => '', 'id' => 0 ) );  

/*
        Initialisation de la liste 
*/

$date = date_format_traitement($date);

$realdate = $datepast = ''; 
mes_date($date, 200, $realdate, $datepast);

$ls = new ls_evenement(array(
        'champ' => EVCH_DATE|EVCH_CAT|EVCH_LIEU|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_TARIF|EVCH_TOUTE_DATE,
        'fi_date_min' => $realdate, 
        'fi_date_max' => NULL, 
        'fi_actif' => 1, 
        'fi_grpstr' => $id, 
	'mode' => reqo::NORMAL,
) );  

$ls->requete(); 

$donne = req('SELECT nom FROM structure_grp WHERE id='.(int)$id.' LIMIT 1 ');
$do = fetch($donne); 

$excel = new ExportDataExcel('browser');
$excel->filename = str2url($do['nom']).".xls";

$excel->initialize();
$excel->addRow(array('titre', 'description', 'contact titre', 'contact tel', 'contact site', 'contact structure nom', 'categorie', 'tarif', 'public', 'dates', 'lieux') ); 

while($ev = $ls->parcours() )
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
