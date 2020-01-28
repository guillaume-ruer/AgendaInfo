<div id="bloc_drapeau" >
	<!-- enlever les drapeaux <a href="<?php echo $url_drapeau_fr; ?>" >
	<img src="<?php echo C_BOUTON; ?>infolimo-drapeau-fr-bj.jpg" 
		alt="Bouton, drapeau fr " /></a>
	<a href="<?php echo $url_drapeau_en; ?>" ><img src="<?php echo C_BOUTON; ?>infolimo-drapeau-en-bj.jpg" 
		alt="Bouton, drapeau en" /></a>-->
</div>

<p id="ladate" ><span class="gras" ><?php echo $date_affiche; ?></span></p>

<form action="<?php echo str_replace('http', 'https', ADD_SITE); ?>index.php?tp=1" method="post" >
<p id="filtre" >
	Date : 
	<img src="jscalendar/img.gif" alt="" id="DateDeb_trigger" />
	<input name="Date" size="12" type="text" id="Date" value="<?php echo $date_champ; ?>" disabled="disabled" style="color:black" />
	<input name="DateDeb" size="12" type="hidden" id="DateDeb" value="<?php echo $date_champ; ?>" onchange="Date.value = DateDeb.value" />
	<script type="text/javascript" >
	var moi = <?php echo $js_moi; ?>; 
	var jr = <?php echo $js_jr; ?>;
	var annee = <?php echo $js_annee; ?>;
	</script>
	<script type="text/javascript" src="<?php echo C_JAVASCRIPT.'init_calendrier.js'; ?>" ></script>

	Thème : 
	<select name="theme" >
		<option value="" >Choisir une thème</option>
	<?php while( $t = $tab_theme->parcours() ) : ?>
		<option value="<?php echo $t->id ?>" <?php selected($t->id == $theme ) ?> > <?php echo $t->nom ?></option>
	<?php endwhile  ?>
	</select>
	<input type="hidden" name="c" value="<?php echo $code; ?>" />
	<input type="hidden" name="l" value="<?php echo ID_LANGUE; ?>" />
	<input type="submit" name="ok" value="Je trouve !" />
	<a href="<?php echo $url_pdf ?>" ><img src="<?php echo C_BOUTON ?>bouton-pdf-a.png" 
		onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>bouton-pdf-b.png';" 
		onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>bouton-pdf-a.png'; " /></a>
</p>
</form>


<div id="liste_evenement" >
<?php while($ev = $lsevent->parcours() ) : ?>
	<div class="symbole" >
		<?php $ev->acc_categorie()->aff() ?><br />
	</div>

	<?php if( $ev->acc_image() ) : ?>
	<div class="event_image" >
		<img src="<?php ps( C_EVENT_IMAGE.$ev->acc_image() ) ?>" />
	</div>
	<?php endif ?>

	<p class="event_date" ><span class="date" ><?php echo $ev->aff_date(); ?></span>
	- <?php if( $ev->acc_nb_date() > 1 ) : ?>
		<a href="<?php $ev->aff_url_autre_date() ?>" target="_blank" >Autres dates</a>
	<?php endif ?>
	<strong class="event_titre" ><?php $ev->aff_titre() ?></strong>
	- 
	<?php $virg=''; foreach($ev->acc_tab_lieu() as $v ) : 
		echo $virg, '<a href="', secuhtml($v->acc_url() ), '" target="_blank" >',secuhtml( $v->acc_nom() ), 
			'&nbsp;(',secuhtml( $v->acc_dep()->acc_num() ) ,')</a>'; 
		if(empty($virg) ) : $virg=', '; endif;
	endforeach ?>
	</p>
	
	<p class="event_description" ><?php $ev->aff_desc() ?></p>
	
	<p class="event_contact" >
		<?php ps( $ev->acc_contact()->acc_structure()->acc_nom() ) ?>
		<?php ps( $ev->acc_contact()->acc_titre() ) ?>
		<?php $ev->aff_source() ?>	
		<?php ps( $ev->acc_contact()->acc_tel() ) ?>

		<?php if( $ev->acc_contact()->acc_site() != '' ) : ?>	
			<a href="<?php $ev->acc_contact()->aff_site() ?>" target="_blank" ><?php $ev->acc_contact()->aff_site() ?></a>
		<?php endif ?>

		<a href="<?php echo $ev->acc_contact()->acc_url() ?>" target="_blank" ><img 
			src="<?php echo C_BOUTON; ?>infolimo-info-bj.gif" alt="infos-structure" 
			title="Plus d'infos sur la structure qui a mis en diffusion cette info" 
			onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-infob-bj.gif';" 
			onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-info-bj.gif'; " /></a>
		<a href="http://www.facebook.com/share.php?u=info-limousin.com/page/autre-date.php?id=<?php echo $ev->acc_id() ?>" 
			target="_blank" ><img src="<?php echo C_BOUTON; ?>infolimo-facebook-bj.gif" 
			alt="Diffuser-Facebook" title="Diffusez cette info sur votre compte Facebook"
			onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-facebookb-bj.gif';" 
			onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-facebook-bj.gif'; "/></a>
	</p>
	
	<hr />
<?php endwhile ?>
</div>

<div class="contient_pagin" >
	<p class="pagination" >
		pages &gt; 
		<?php $lsevent->acc_pagin()->affiche() ?>
		&lt; pages 
	</p>
</div>

<div id="annonce">
	<p>Abonnez-vous au flux RSS de cet agenda 
	<a href="<?php echo $url_rss ?>" target="_blank" >
	<img src="http://www.info-limousin.com/img/flux-rss.gif" alt="flux rss info limousin" width="120" height="17" /></a>
	</p>
	<p><a href="http://agenda-dynamique.com" target="_blank" >
		<img src="http://www.info-limousin.com/img/logo_200x30.gif" alt="agenda_dynamique" /></a>
	</p>
</div>
