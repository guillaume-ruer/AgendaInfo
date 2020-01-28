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

	<div id="map" ></div>

	<script type="text/javascript" >

	var initMap = function ()
	{
		var map = new google.maps.Map(document.getElementById('map'),
		{
			zoom:<?php ps($taille_google) ?>,
			mapTypeId: 'hybrid'
		});
		var address = '<?php ps( $google ) ?>';
		var geocoder = new google.maps.Geocoder();

		geocoder.geocode( { 'address' : address }, function( results, status ) {
			if( status == google.maps.GeocoderStatus.OK ) {
				

				//In this case it creates a marker, but you can get the lat and lng from the location.LatLng
				map.setCenter( results[0].geometry.location );

				var marker = new google.maps.Marker( {
					map     : map,
					position: results[0].geometry.location
				} );
			} 
			else 
			{
				alert( 'Geocode was not successful for the following reason: ' + status );
			}
		});
	}; 

	</script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $APIKEY ?>&callback=initMap"
        async defer></script>
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

<div class="google" ><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- annonce bas site structures -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-3153780600812349"
     data-ad-slot="5519111116"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></div>



