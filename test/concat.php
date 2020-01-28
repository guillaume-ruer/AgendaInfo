<?php
include '../include/init.php'; 

req('SET SESSION group_concat_max_len= 16384 ');

$donne = req("
            SELECT lg.id, lg.nom , GROUP_CONCAT( 
                'titre=', l.titre, '_&_url=', l.url, '_&_img=', l.img 
                ORDER BY l.titre
                SEPARATOR ';;' 
            ) lien_ls 
            FROM lien_grp lg
             JOIN lien l ON l.type=lg.id 
            WHERE 1 
             GROUP BY lg.id 
             HAVING COUNT(*) > 0 
            ORDER BY lg.nom
");

while($do = fetch($donne) )
{
    imp($do); 
}
