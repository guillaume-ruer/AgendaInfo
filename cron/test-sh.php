<?php
include '../include/init.php'; 

define('INTERVAL', 20); 

$page = 0; 
$tmp = $page * INTERVAL;
$f = $tmp + 1 ; 
$t = $tmp + INTERVAL;
$mois = 5; 

$dest = C_DOS_PHP.'flux_lei/p'.$page.'.xml';
$fichier = 'http://www.tourisme-limousin.net/applications/xml/exploitation/listeproduits.asp'
    .'?rfrom='.$f.'&rto='.$t.'&user=2000033&pwkey=3a2b967c716113be7ecf4120501c031e&urlnames=tous&'
    .'PVALUES='.urlencode('30000006,@DJ,+'.$mois.'M')
    .'&PNAMES='.urlencode('elgendro,horariodu,horarioau')
    .'&clause=2000033000018' ; 

if( copy($fichier,  $dest) )
{
    $var = "copy fonctionne"; 
}
else
{
    $var = "copy ne fonctionne pas !"; 
}

if( file_get_contents($fichier) )
{
    $var2 = "file_get_contents fonctionne !"; 
}
else
{
    $var2 = "file_get_contents ne fonctionne pas !"; 
}

if( mail('strategeyti@gmail.com', 'Cron Info-limousin', $v= 'Salut, voici un message du cron Info-Limousin, via sh ('.$var.') ('.$var2.')') )
{   
    echo "Mail envoyé\n"; 
}
else
{
    echo "Mail non envoyé\n"; 
}

echo "$v\n"; 
