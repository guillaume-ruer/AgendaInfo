<div id="fond_aff" class="fixe" >
<?php while( $do = $affichette->parcours() ) : ?>
		<?php affichette_aff( $do, FALSE ) ?>
	<?php endwhile ?>
</div>

<!-- Le panneau centrale -->
<div id="fond_filtre" class="fixe" >
<div id="info_gauche" ><a href="http://agenda-dynamique.com" ><img src="../img/logo_info_limousin_2018.jpg" alt="agenda dynamique info limousin" hspace="0" 
	vspace="0" border="0" align="middle" longdesc="adhésion à l'association info Limousin" /></a></div> 



	<div id="formulaire" >
	<form action="index.php" method="post" >
		
		<p>
			<label for="dateDeb_trigger" ><?php l('cherche2'); ?> :&nbsp;</label>
			<img src="jscalendar/img.gif" alt="" id="DateDeb_trigger" />
			<input type="text" id="calendar-inputField" disabled="yes" value="<?php echo $date_champ ?>" style="color:black" />
			<input type="hidden" id="champ_date" name="DateDeb" value="<?php echo $date_champ ?>" />
			<script type="text/javascript" >
			Calendar.setup({
				trigger    : "DateDeb_trigger",
				inputField : "calendar-inputField",
				min: <?php echo date('Ymd') ?>,
				max: <?php echo $js_max ?>,
				align:"Br",
				dateFormat : "%d/%m/%Y",
				animation : false, 
				onSelect   : function() { 
					$("#champ_date").val( $("#calendar-inputField").val() );
					this.hide() 
				}
			});
			</script>
		</p>
	
		<!-- Début des menu déroulant par zone géographique -->
		
		<p>
			<label for="groupe_lieu" >Groupe de lieux :&nbsp;</label>
			<select class="md_filtre" name="groupe_lieu" id="groupe_lieu" onchange="javascript:deux_deroulant_defaut('lieu', 'lieu_spe');">
			<?php foreach($tab_groupe_lieu as $do ){ extract($do); ?>
				<option value="<?php echo $value; ?>" <?php echo $s; ?> > <?php echo $nom; ?></option>
			<?php } ?>
			</select>
			<?php $ch_lieu->label ?>
			<?php $ch_lieu->champ ?>
		  </p>

	  
		<p>
		<label for="md_theme" >Thèmatiques :&nbsp;</label>
			<select id="md_theme" name="theme" class="md_filtre" >
			<?php foreach($tab_theme as $do ){ extract($do); ?>
				<option value="<?php echo $id; ?>" <?php echo $s; ?> > <?php echo $nom; ?></option>
			<?php } ?>
			</select>
		</p>
		<p class="aligncenter" ><input type="hidden" name="l" value="<?php echo '<?php echo ID_LANGUE; ?>'; ?>" />
		<div id="lien_rss">
			<input type="submit" class="bouton" name="ok" value="LANCER LA RECHERCHE" /> 
			<a href="<?php echo $rss; ?>" >Flux rss</a> 
			<a href="<?php echo $lien_pdf ?>" >Fichier PDF</a> 
		</div><br />
		</p>

	</form>
	</div>
	</div>
		
</div>


<!-- Le contenu de la recherche, avec des infos sur le côté -->
<div class="fixe" >
	<div id="bloc_evenement" >
		<!-- le résultats du filtrage -->
		<?php include C_PATRON.'evenement.p.php'; ?>
	</div>
</div>
    
<!-- Pagination Basse -->
<div class="fixe" >
	<?php $lsevent->acc_pagin()->affiche() ?>
</div>

<!-- Ligne fuyante -->
<div class="google" ><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- bandeau largeur bas du site -->
	<ins class="adsbygoogle"
	     style="display:block"
	     data-ad-client="ca-pub-3153780600812349"
	     data-ad-slot="5929741510"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
</div>
