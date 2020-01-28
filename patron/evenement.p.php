<!-- Info sur le filtrage -->
<h1 id="info_filtrage" >Tri : <?php echo $info_filtrage; ?></h1>	

<div id="liste_evenement" >
<?php while ($ev = $lsevent->parcours() ) :  ?>
<div class="bloc" >
	<div class="symbole" >
		<?php $ev->acc_categorie()->aff() ?>
	</div>

	<div class="event_image" >
	<?php if( $ev->acc_image() ) : ?>
		<img src="<?php ps( C_EVENT_IMAGE.$ev->acc_image() ) ?>" />
	<?php elseif( $ev->acc_contact()->acc_structure()->acc_logo() ) : ?>
		<img src="<?php ps( RETOUR.'img/logos/'.$ev->acc_contact()->acc_structure()->acc_logo() ) ?>" />
	<?php endif ?>
	</div>

	<h3 class="event_date" ><span class="date" ><?php $ev->aff_date() ?></span>
	- <?php if( $ev->acc_nb_date() > 1 ) : ?>
		<a href="<?php $ev->aff_url_autre_date() ?>" >Autres dates</a>
	<?php endif ?>
	<strong class="event_titre" ><?php $ev->aff_titre() ?></strong>
	- 
	<?php $virg=''; foreach($ev->acc_tab_lieu() as $v ) : 
		echo $virg, '<a href="', secuhtml($v->acc_url() ), '" >',secuhtml( $v->acc_nom() ), 
			'&nbsp;(',secuhtml( $v->acc_dep()->acc_num() ) ,')</a>'; 
		if(empty($virg) ) : $virg=', '; endif;
	endforeach ?>
	</h3>

	<p class="event_description" ><?php $ev->aff_desc() ?></p>

	<p class="event_contact" ><span class="texte_contact" >
		<?php ps( $ev->acc_contact()->acc_structure()->acc_nom() ) ?>
		<?php ps( $ev->acc_contact()->acc_titre() ) ?>
		<?php $ev->aff_source() ?>	
			<?php ps( $ev->acc_contact()->acc_tel() ) ?>
			<?php if( $ev->acc_contact()->acc_site() != '' ) : ?>	
				[<a href="<?php $ev->acc_contact()->aff_site() ?>" ><?php $ev->acc_contact()->aff_site() ?></a>]
			<?php endif ?>

			</span>
		<a href="<?php echo $ev->acc_contact()->acc_url(); ?>" >
		<img src="<?php echo C_BOUTON; ?>infolimo_structure_a_2013.gif" alt="info-structure" 
			title="Plus d'infos sur la structure qui a mis en diffusion cette info" 
			onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>infolimo_structure_b_2013.gif';" 
			onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>infolimo_structure_a_2013.gif'; " /></a> 
		<a href="http://www.facebook.com/share.php?u=info-limousin.com/page/autre-date.php?id=<?php echo $ev->acc_id() ?>" >
		<img src="<?php echo C_BOUTON; ?>infolimo_facebook_a_2013.gif" alt="Diffuser-Facebook" 
			title="Diffusez cette info sur votre compte Facebook" 
			onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>infolimo_facebook_b_2013.gif';" 
			onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>infolimo_facebook_a_2013.gif'; " /></a>

		<?php if( $ev->acc_contact()->acc_structure()->acc_code_externe() ) : ?>
		<a href="<?php echo ADD_SITE.'externe/'.$ev->acc_contact()->acc_structure()->acc_code_externe() ?>/0_0_FR.rss" >
		<img src="<?php echo C_BOUTON; ?>infolimo_relais_a_2013.gif" alt="Diffuser-Facebook" 
			title="Flux rss de la structure" 
			onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>infolimo_relais_b_2013.gif';" 
			onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>infolimo_relais_a_2013.gif'; " />
		</a>
		<?php endif ?>
	</p></div>
	<hr />
<?php endwhile ?>
</div>

