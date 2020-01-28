<?php
require '../include/init.php'; 
require C_INC.'fonc_cache.php'; 

header('content-type: text/xml charset="utf8" ');
define('TPS_CACHE', MODE_DEV ? 0 : 1800); 
define('NB_JOUR_VISIBLE', 120); 

$input_date = (isset($_GET['date']) ) ? $_GET['date'] : date('Y-m-d'); 
$realdate = $datepast = ''; 
mes_date($input_date, NB_JOUR_VISIBLE, $realdate, $datepast);

$tab_lieu = opt_lieu($realdate, $datepast); 
$tab_groupe_lieu = opt_groupe_lieu($realdate, $datepast); 
$tab_theme = opt_theme_non_vide($realdate, $datepast); 

$id_cache = cache_id($input_date);

if(cache($id_cache, TPS_CACHE  ) ) {
?>
<?php echo '<?php echo "<?xml version=\"1.0\" ?>" ?>' ?>
<option>
<meta>
	<donne name="date_debut" value="<?php echo $realdate ?>" />
	<donne name="date_fin" value="<?php echo $datepast ?>" />
	<donne name="date_jour" value="<?php echo date('Y-m-d') ?>" />
</meta>
<donne>
<liste-lieu>
<?php foreach( $tab_lieu as $lieu ) : ?>
	<?php if($lieu['value'] != 0 ) : ?>
	<lieu id="<?php echo $lieu['value'] ?>" ><?php echo $lieu['nom'] ?></lieu>
	<?php endif ?>
<?php endforeach ?>
</liste-lieu>
<liste-groupe-lieu>
<?php foreach( $tab_groupe_lieu as $gl ) : ?>
	<?php if($gl['value'] != 0 ) : ?>
	<groupe-lieu id="<?php echo $gl['value'] ?>" ><?php echo $gl['nom'] ?></groupe-lieu>
	<?php endif ?>
<?php endforeach ?>
</liste-groupe-lieu>
<liste-theme>
<?php foreach( $tab_theme as $theme ) : ?>
	<?php if($theme['id'] != 0 ) : ?>
	<theme id="<?php echo $theme['id'] ?>" ><?php echo $theme['nom'] ?></theme>
	<?php endif ?>
<?php endforeach ?>
</liste-theme>
</donne>
</option>

<?php
// Fin cache 
}
cache(); 
