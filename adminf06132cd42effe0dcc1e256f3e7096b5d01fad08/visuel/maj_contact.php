<?php
include '../../include/init.php'; 
include C_INC.'ls_contact_class.php'; 
include C_INC.'reqa_class.php'; 

header('Content-type: text/xml'); 


if( !empty($_GET['ids']) )
{
	$lc = new liste_contact; 
	$lc->fi_contact_structure = absint($_GET['ids']); 
	$lc = $lc->requete(); 
}

?>
<?php echo '<?xml version="1.0" encoding="utf-8" ?>' ?>

<lscontact>
	<?php if( !empty($_GET['ids']) ) : ?>
		<?php while( $l = $lc->parcours() ) : ?>
			<contact donne="<?php echo $l->titre, ' ', $l->tel, ' [', $l->site, ']' ?>" id="<?php echo $l->id ?>" />
		<?php endwhile ?>

	<?php endif ?>
</lscontact>
