<?php 
require '../../include/init.php'; 

/*
for($ti=[], $i=0; $i<100; $i++)
{
	$fourche = 3600*24*31*12; 
	$time = rand(time() - $fourche, time()+$fourche); 
	$ti[] = '('.$time.',\'Teste\',\'127.0.0.1\')'; 
}

req('INSERT INTO planning_stat(`date`,user_agent,ip)VALUES'.implode(',',$ti) );
*/

$donne = req('
	SELECT COUNT(*) c, FROM_UNIXTIME(`date`, \'%m-%Y\') d
	FROM planning_stat
	GROUP BY FROM_UNIXTIME(`date`, \'%Y%m\')
	ORDER BY `date` DESC
');

$tab = []; 

while($do = fetch($donne) )
{
	$tab[] = [$do['d'],$do['c']];
}

$lse = new ls_evenement();
$lse->mut_fi_planning_stat(TRUE); 
$lse->requete(); 

require PATRON; 
