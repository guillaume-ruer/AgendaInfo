<?php while( $ev = $lsevent->parcours() ) : ?>
<item>
	<title><?php $ev->aff_date() ?> - <?php $ev->aff_titre() ?> - 
	<?php $virg=''; foreach($ev->acc_tab_lieu() as $v ) : 
		echo $virg, secuhtml( $v->acc_nom() ), ' (',secuhtml( $v->acc_dep()->acc_num() ) ,')'; 
		if(empty($virg) ) : $virg=', '; endif;
	endforeach ?>
	</title>
	<description> 
		<?php if( $ev->acc_image() ) : ?>
		<![CDATA[ <img src="<?php echo ADD_SITE.C_EVENT_IMAGE.$ev->acc_image() ?>" /> ]]>
		<?php endif ?>
		<?php $ev->aff_desc(FALSE) ?> Contact : 
		<?php ps( $ev->acc_contact()->acc_structure()->acc_nom() ) ?>
		<?php ps( $ev->acc_contact()->acc_titre() ) ?>
		<?php $ev->aff_source() ?>
		<?php ps( $ev->acc_contact()->acc_tel() ) ?>
		<?php $ev->acc_contact()->aff_site() ?>
	</description>
	<link ><?php echo strpos($baseurl, '%id') !== FALSE ? str_replace('%id', $ev->acc_id(), $baseurl) : $baseurl ?></link>
</item>
<?php endwhile ?>
