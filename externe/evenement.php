<?php
require '../include/init.php'; 
require C_INC.'evenement_fonc.php'; 
require C_INC.'fonc_cache.php'; 

function url_image($e)
{
	$ui = ''; 
	if( $e->acc_image() ) 
	{
		$ui = secuhtml( 'http://info-limousin.com/dos-php/event_image/'.$e->acc_image() );
	}
	elseif( $e->acc_contact()->acc_structure()->acc_logo() ) 
	{
		$ui= secuhtml('http://info-limousin.com/img/logos/'.$e->acc_contact()->acc_structure()->acc_logo() ); 
	}
	return $ui; 
}

http_param(array('id' => 0 ) ); 

if( !( !empty($id) && ($e = event_init($id) ) ) )
{
	exit('Aucun'); 
}

$idc = cache_id($id); 
header('content-type: text/xml charset="utf8" ');

if( cache($idc, 1800) )
{

?>
<?php echo '<?php echo "<?xml version=\"1.0\" ?>" ?>' ?>

<evenement id="<?php $e->aff_id() ?>" >
	<titre><?php $e->aff_titre() ?></titre>
	<description><?php $e->aff_desc(FALSE) ?></description>
	<ls-date >
	<?php foreach($e->acc_tab_date() as $num => $date ) : ?>
		<bl-date>
		<date format="text" lng="fr" ><?php $e->aff_date($num) ?></date>
		<date format="yyyy-mm-dd" ><?php echo $date ?></date>
		</bl-date>
	<?php endforeach ?>
	</ls-date>
	<image><?php echo url_image($e) ?></image>
	<contact id="<?php $e->acc_contact()->aff_id() ?>" >
		<titre><?php $e->acc_contact()->aff_titre() ?></titre>	
		<lien><?php $e->acc_contact()->aff_site() ?></lien>	
		<tel><?php $e->acc_contact()->aff_tel() ?></tel>	
		<structure id="<?php $e->acc_contact()->acc_structure()->aff_id() ?>" >
			<nom><?php $e->acc_contact()->acc_structure()->aff_nom() ?></nom>
		</structure>
	</contact>
	
	<categorie id="<?php echo $e->acc_categorie()->acc_id() ?>" >
		<nom><?php echo $e->acc_categorie()->acc_nom() ?></nom>
		<img><?php echo $e->acc_categorie()->acc_img() ?></img>
		<groupe id="<?php echo $e->acc_categorie()->acc_groupe() ?>" >
			<?php echo $e->acc_categorie()->acc_groupe_nom() ?>
		</groupe>
	</categorie>
	<liste-ville>
	<?php foreach($e->acc_tab_lieu() as $lieu) : ?>
		<ville id="<?php $lieu->aff_id() ?>" >
		<nom><?php $lieu->aff_nom() ?></nom>
		</ville>
	<?php endforeach ?>
	</liste-ville>
</evenement>
<?php 
} 
cache();
?>
