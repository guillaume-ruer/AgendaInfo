<div id="pos_cal" >
	<div id="pos_fix_cal" >
		<div id="titre_cal" ></div>
		<div id="bl_cal" class="bl_cal_redui block" >
			<div id="cal_menu" ><a id="cal_mode" ><img src="http://info-limousin.com/img/planning/bouton_ouverture.png" /></a><div id="cal_menu_bt" ></div></div>

			<div id="cal_mois" >
			<?php foreach( $tabm as list($m, $a, $mnom, $jours) ) : ?>

			<div class="mois" data-mois="<?php echo $m ?>" data-annee="<?php echo $a ?>" >
				<div class="mois-nom" ><?php echo $mnom.' '.$a ?></div>

				<?php foreach($jours as list($num, $nbe, $we, $l, $date, $fut) ) : ?>
					<div class="jour <?php if($we) : ?>we<?php endif ?> <?php if(!$fut) : ?>passe<?php 
						endif ?> <?php if($date == date('Y-m-d') ) : ?>actuel<?php endif ?>" ><!--
					--><a class="bt_jr" href="<?php echo ADD_SITE_DYN.$num.'-'.$m.'-'.$a.'/p1.html' ?>" >
						 <span class="num" ><?php echo $num ?></span><!--
						--><span class="jl" ><?php echo $l  ?></span>
					</a><!--
					--><span class="jour_sep" >
						<span class="nbe" ><?php echo $nbe ?></span><span class="nbs" ></span>
					</span>
					</div>
				<?php endforeach ?>
			</div>
			<?php endforeach ?>
			</div>
		</div>
	</div>
</div>

<div id="bl_page" >
	<div id="banderol" >
		<?php if($banderol) : ?>	
		<a href="<?php echo RETOUR ?>agenda-dynamique/banderol.php?id=<?php echo $banderol['id'] ?>" ><img src="<?php echo C_DOS_PHP.'banderol/'.$banderol['img'] ?>" /></a>
		<?php endif ?>
	</div>
	<div id="bl-bandeau" >
		<div id="sel_zone" class="block" >
			commune <input id="ch_lieu" type="text" name="lieu" />
			rayon <select id="rayon" >
			<?php for($i=0; $i<=100; $i+=($i>=20 ? 10 :5)) : ?>
				<option value="<?php echo $i ?>" ><?php echo $i ?></option>
			<?php endfor ?>
			</select>km
		</div><!--

		--><a class="block" id="bt-aide" href="#" ><span id="bt-aide-txt" >aide dynamique</span></a><!--
        
		--><a class="block" id="bt-adhesion" href="https://www.asso.info-limousin.com/association/adhesion" ><span id="bt-adhesion-txt" >adhérer pour diffusion</span></a><!--

		--><div id="sel_date" >
			Dates <br />du : <input id="datedu" type="text" size="10" /> <br />au : <input id="dateau" type="text" size="10" />
		</div><!--
		--><div id="bl_bt" >
			<form id="gen-planning" action="<?php echo ADD_SITE ?>externe/planning-pdf.php" method="post" >
				<input id="planning-param" type="hidden" name="param" value="" />
				<a id="planning" ><span id="bt_planning_img" ></span><span id="bt_planning_txt" >télécharger mon planning en fichier <span class="couleur_marque" >PDF</span></span></a>
			</form>
		</div>
	</div>

	<div id="bl_droite" >
		<div id="map" class="block" ></div>
	</div>

	<div id="barre_central" >
		<div id="liste_theme" >
			<div class="theme" >
			<a id="theme_tous_decoche" >
			<div class="theme_img" ><img src="<?php echo ADD_SITE_DYN.'../' ?>img/symboles/tout-decoche.png" alt="Tout décocher" title="Tout décocher" /></div></a>
			<div class="theme_img" ><a id="theme_tous_coche" ><img src="<?php echo ADD_SITE_DYN.'../' ?>img/symboles/tout-coche.png" alt="Tout cocher" title="Tout cocher" /></div></a>
			</div>

		<?php foreach($tab_theme as $theme) : ?>
			<div class="theme" >
			<label>
			<div class="theme_img" ><?php $theme->aff() ?></div><br />
			<input type="checkbox" name="tab_theme[]" value="<?php $theme->aff_id() ?>" checked="checked" />
			</label>
			</div>
		<?php endforeach ?>
		<?php foreach(remarquable::$TAB_TYPE as $id_type => $theme) : ?>
			<div class="theme" >
			<label>
			<div class="theme_img" ><img src="<?php echo ADD_SITE_DYN.'../img/groupe-remarquable/'.$theme['img'] ?>" title="<?php echo $theme['nom'] ?>" /></div><br />
			<input type="checkbox" name="tab_type_rem[]" value="<?php echo $id_type ?>" checked="checked" />
			</label>
			</div>
		<?php endforeach ?>
		</div>

	</div>

	<div id="liste" >
		<div>
			<div id="prop" >Proposition (<span id="prop_nb" ><?php echo $lse->acc_nb_entre() ?></span>)</div><!--
			--><div id="lr" >Lieux remarquables (<span id="lr_nb" >0</span>)</div><!-- 
			--><div id="sel" >Sélection (<span id="sel_nb" >0</span>)</div>
		</div>
		<div>
			<div class="liste"  id="onglet_prop" >
				<div id="barre_filtre" class="barre_filtre" ></div>
				<div class="pagin-prop" ><?php $lse->acc_pagin()->aff_dyn() ?></div>
				<div id="liste_proposition" >
				<?php while($ev = $lse->parcours() ) : ?>
					<?php echo $ev->planning_html($param) ?>
				<?php endwhile ?>
				</div>
				<div class="pagin-prop" ><?php $lse->acc_pagin()->aff_dyn() ?></div>
			</div>
			<div class="liste" id="onglet_lr" >
				<div id="barre_filtre_rem" class="barre_filtre" ></div>
				<div id="liste_lr" >
					<?php echo $res['lr_mess'] ?>	
				</div>
				<div id="pagin-lr" >
					<input id="bt_plus_lr" type="button" value="Voir plus" />
				</div>
			</div>
			<div class="liste" id="liste_selection" ></div>
		</div>
	</div>

	<div id="bl-pied-page" >

		<script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- Bandeau nouvel agenda 2016 -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-3153780600812349"
			 data-ad-slot="5872507517"
			 data-ad-format="auto"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>

		<div id="contient_logo" >
			<a href="http://exor-site.com" target="_blank" ><span id="bl_exor_site" ><img src="<?php echo ADD_SITE_DYN ?>../img/planning/exor-site-logo.svg" /><strong id="exor_site_txt" >Développement<br />Maintenance<br />Exor-Site.com</strong></a>
			<a href="https://www.sirtaqui-aquitaine.com" target="_blank" ><img src="<?php echo $ch_logo ?>logo_sirtaqui_aquitaine.jpg" alt="Sirtaqui Aquitaine" name="sirtaqui" id="sirtaqui" /></a>
            <a href="http://www.tourismelimousin.com" target="_blank" ><img src="<?php echo $ch_logo ?>logo-lei-limousin.jpg" alt="LEI - Comité régional du tourisme du Limousin" name="LEI" id="LEI" /></a>
			<a href="http://www.pnr-millevaches.fr" target="_blank" ><img src="<?php echo $ch_logo ?>logo-pnr-millevaches.jpg" alt="pnr millevaches" name="PNRMillevaches" id="PNRMillevaches" /></a>
			<a href="https://www.nouvelle-aquitaine.fr"><img src="<?php echo $ch_logo ?>logo-region.png" alt="région Nouvelle Aquitaine" name="RegionNouvelleAquitaine" id="RegionNouvelleAquitaine"/></a> 
			<a href="http://www.ville-limoges.fr" target="_blank" ><img src="<?php echo $ch_logo ?>logo_limoges.jpg" alt="ville limoges" name="Limoges" id="Limoges" /></a>
			<a href="http://www.ville-gueret.fr" target="_blank" ><img src="<?php echo $ch_logo ?>logo-gueret.jpg" alt="ville guéret" name="Gueret" id="Gueret" /></a> 
			<a href="http://creuse-grand-sud.fr/" target="_blank" ><img src="<?php echo $ch_logo ?>logo_creuse_grand_sud.jpg" alt="Creuse Grand Sud" name="CreuseGrandSud" id="CreuseGrandSud" /></a> 
			<a href="http://www.mairie-eymoutiers.fr/" target="_blank" ><img src="<?php echo $ch_logo ?>logo_eymoutiers.jpg" alt="ville eymoutiers" name="Eymoutiers" id="Eymoutiers" /></a>
			<a href="http://www.meymac.fr/" ><img src="<?php echo $ch_logo ?>logo_meymac.jpg" alt="ville Meymac" name="Meymac" id="Meymac" /></a>
		</div>

		<p id="pieds_de_page" > Les informations pr&eacute;sentes sont saisies par les structures organisatrices et les offices de tourisme, les modifications se font en temps réel.<br />
		<a href="http://www.asso.info-limousin.com" target="_blank" >Association Info Limousin</a> 
		<a href="http://agenda-dynamique.com/membre/connexion.php" target="_blank" >Accès à la plate-forme de diffusion</a> 
		</p>
	</div>
</div>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-78077272-1', 'auto');
  ga('send', 'pageview');

  <?php // Donnée php -> js ?> 

</script>

<div style="display:none;" >
	<div id="tab_lieu" ><?php echo JSON_encode($tab_lieu) ?></div>
	<div id="date1" ><?php echo $datedu ?></div>
	<div id="date2" ><?php echo $dateau ?></div>
</div>

<script src="<?php echo ADD_SITE_DYN.'../javascript/' ?>planning.php" ></script>

