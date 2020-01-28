<!-- Ligne fuyante -->
<div class="fond_souligne" ></div> 

<div class="fixe" >
	<div id="bouton_retour" >
		<a href="../" >Agenda</a> - <a href="<?php echo ancien_url(); ?>" >Retour</a> - <?php ps( $ville->acc_nom() ) ?>
	</div>
</div>

<!-- Ligne fuyante --><div class="fond_souligne" ></div>

<div class="fixe" >
	<div id="texte_lieu">
		
		
		<h1><?php ps( $ville->acc_nom() )?>, <?php ps( $ville->acc_dep()->acc_nom() ) ?></h1>
		
		<p class="event_liens" >
		<?php if( $ville->acc_site() ) : ?>
			Site Internet du lieu : <a href="<?php ps($ville->acc_site()) ?>" ><?php ps($ville->acc_site()) ?></a>
			<br />
		<?php endif ?>

		<?php if( $ville->acc_facebook() ) : ?>
			Page Facebook : <a href="<?php ps($ville->acc_facebook() )?>" >http://www.facebook.com</a>
			<br />
		<?php endif  ?>
		
		<?php if( $ville->acc_wikipedia() ) : ?>
			Page Wikipédia : <a href="<?php ps($ville->acc_wikipedia() ) ?>" ><?php ps($ville->acc_wikipedia() ) ?></a>
			<br />
		<?php endif  ?>
	
		<?php if( $ville->acc_mairie() ) :  ?>
			Téléphone de la mairie : <?php ps($ville->acc_mairie() ) ?> 
			<br />
		<?php endif  ?>

		<?php if( $ville->acc_office() ) : ?>
			Téléphone de l’Office de tourisme : <?php ps( $ville->acc_office() ) ?>
		<?php endif ?>
		</p>
	</div>
	
	<div id="map" >

		<script type="text/javascript" >

		var initMap = function ()
		{
			var map = new google.maps.Map2(document.getElementById('map'));
			map.addControl(new GSmallMapControl()); 
			map.addControl(new GMapTypeControl());

			var adresse = '<?php ps( $ville->acc_nom() ) ?>, Limousin, <?php ps( $ville->acc_google_map() ) ?>';
			var geocoder = new google.maps.ClientGeocoder();
				geocoder.getLatLng(adresse, function (coord) {
				map.setCenter(coord, 11); 
			});

		};  
		google.load("maps", "2");
		google.setOnLoadCallback(initMap);
		</script>
	</div>
</div>

<!-- Ligne fuyante --><div  class="fond_souligne" ></div>


<div class="fixe" >
	<div id="col_gauche" >
		<!-- le résultats du filtrage -->
		<?php include C_PATRON.'evenement.p.php'; ?>
	</div>


	
</div>

<!-- Ligne fuyante --><div  class="fond_souligne" ></div>

<div class="fixe" >
<!-- Pagination Basse -->
	<?php $lsevent->acc_pagin()->affiche() ?>
</div>	

<div class="google" ><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- bandeau largeur bas du site -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-3153780600812349"
     data-ad-slot="5929741510"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></div>
