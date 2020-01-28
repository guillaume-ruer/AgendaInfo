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

http_param(array('id' => 0 ) );

$donne = req('SELECT nom FROM structure_grp WHERE id='.(int)$id.' LIMIT 1 ');
$do = fetch($donne); 

$excel = new ExportDataExcel('browser');
$excel->filename = 'adresse-'.str2url($do['nom']).".xls";

$excel->initialize();

$donne = req('
	SELECT s.nom, s.adresse, v.Lieu_Ville, Lieu_CP
	FROM structure s
	JOIN structure_grp_structure sg
		ON sg.id_structure = s.id 
	LEFT OUTER JOIN Lieu v
		ON s.ville = v.Lieu_ID
	WHERE sg.id_structure_grp='.$id.'
');

$excel->addRow(array('Structure', 'Adresse', 'Ville', 'CP') ); 

while($do = fetch($donne) )
{
	$excel->addRow($do);
}

$excel->finalize();
