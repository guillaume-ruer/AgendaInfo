<?php
include '../include/init.php'; 
$cwd = getcwd(); 

if( copy('../dos-php/flux_lei/p0.xml', '../dos-php/flux_lei/p1.xml') )
{
    $var = 'copy fonctionne'; 
}
else
{
    $var = 'copy impossible'; 
}

if( mail('strategeyti@gmail.com', 'Cron Info-limousin', $text = 'Salut, voici un message du cron Info-Limousin, via php. ('.$cwd.') ('.$var.')') )
{   
    echo "Mail envoyé\n"; 
}
else
{
    echo "Mail non envoyé\n"; 
}

echo $text."\n"; 
