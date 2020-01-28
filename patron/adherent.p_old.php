<!-- Ligne fuyante -->
<div class="fond_souligne" ></div> 

<div class="fixe" >
	<div id="bouton_retour" >
		<a href="<?php echo RETOUR; ?>" >Agenda</a> - <a href="<?php echo ancien_url(); ?>" >Retour</a> - Autres dates
	</div>
</div>

<!-- Ligne fuyante --><div class="fond_souligne" >
</div>

<div class="fixe" >
	<div id="texte_lieu"  class="event_liens">
		<?php if(!empty($logo) ) : ?>
			<p><img alt="logo adhérent" src="<?php echo C_IMG.'logos/'.secuhtml($logo ) ?>" 
				height="90px" width="160px" /></p>
		<?php endif ?>
		<h1><?php ps($str->acc_nom() ) ?></h1>

		<p class="event_liens" >
			<?php if(!empty($facebook) ) : ?>
				Page Facebook <a href="<?php echo $facebook ?>" >http://www.facebook.com</a> <br />
			<?php endif ?>
		
			<?php if( !empty($addresse) ) : ?>
				Adresse  : <?php ps( $addresse ) ?><br />
			<?php endif ?>
			Contact : 
		</p>
			<ul>
			<?php foreach($str->acc_tab_contact() as $c ) : ?>
				<li><?php ps( $c->acc_titre() ) ?> <?php ps( $c->acc_tel() ) ?> <?php $c->aff_site(TRUE) ?></li>
			<?php endforeach ?>
			</ul>

			<?php if( $str->acc_desc() != '' ) : ?>
				<p><?php echo $str->aff_desc(TRUE) ?></p>
			<?php endif ?>
	</div>

	<div id="map" >

	<script type="text/javascript" >

	var initMap = function ()
	{
		var map = new google.maps.Map2(document.getElementById('map'));
		map.addControl(new GSmallMapControl()); 
		map.addControl(new GMapTypeControl());

		var adresse = '<?php ps( $google ) ?> Limousin';
		var geocoder = new google.maps.ClientGeocoder();
			geocoder.getLatLng(adresse, function (coord) {
			map.setCenter(coord, <?php ps( $taille_google ) ?>); 
		});

		map.setMapType(G_SATELLITE_MAP);
	}; 
	    
	google.load("maps", "2");
	google.setOnLoadCallback(initMap);
	</script>
	</div>

</div>

<!-- le résultats du filtrage -->

<!-- Ligne fuyante --><div  class="fond_souligne" ></div>

<div class="fixe" >
	<div id="col_gauche" >
		<?php include C_PATRON.'evenement.p.php'; ?>
	</div>

	<div id="col_droite" >
		

	</div>
</div>


<!-- Ligne fuyante --><div  class="fond_souligne" ></div>
<div class="fixe" >
<?php $lsevent->acc_pagin()->affiche() ?>
</div>



