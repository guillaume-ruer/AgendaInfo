<?php
require '../include/init.php'; 
require C_INC.'flux_rss_fonc.php'; 

$tab = flux_rss('http://www.ville-limoges.fr/index.php/fr/component/jevents/odandb.rss/rss/?format=feed&fullview=100');

function retire_date($lien)
{
	return preg_replace('`[0-9]{1,2}\s*[a-z]{1,3}\s*[0-9]{1,4}\s*:?`i', '', $lien); 
}
?>
<?php foreach($tab as $e) : ?>
	<div><a href="<?php echo $e['link'] ?>" ><?php echo retire_date($e['title']) ?></a></div>
<?php endforeach ?>
