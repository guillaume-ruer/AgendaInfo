<?php
require '../include/init.php'; 
require C_INC.'fonc_memor.php'; 
?>
$(function(){
	var BREAKPOINT = 700; 
	var nom_mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
		'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

	var nom_jour = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']; 

	var RECUP_TOUT = 0; 
	var RECUP_EVENT = 1; 
	var RECUP_REM = 2; 

	var lsevent = []; // Liste des événements récupéré
	var lsel = []; // Liste des événements dans la séléction
	var tab_theme = []; // Thème séléctionné 
	var tab_type_rem = []; // Thème séléctionné des Sites remarquable 
	var tab_rem = [] ; // Liste des remarquables résultat des filtres
	var tab_tout_rem = []; // Tout les remarquables des filtres
	var date1 = null;
	var date2 = null; 
	var flieu = null; // Filtre par lieu 
	var filtre_lieu = null // Nom du lieu 
	var rayon = null; // Rayon autour du lieu 
	var tab_lieu = []; // Lieux résultants des filtres
	var tabid = []; // Id des événements sélectionnés
	var tabid_img = [] // Id des événements sélectionnés du dernier filtre 
	var $mess = null; // Pour indiquer un message si pas d'événements 
	
	var num_page = 1; 
	var num_page_rem = 0; 

	var nb_prop=0; // Nombre totale de proposition. 
	var nb_page_recup=0; // Nombre de page à récupéré pour les filtres actuel
	var nb_page_recup_rem=0; // Nombre de page à récupéré pour les filtres actuel

	var iw_open = []; // InfoWindow de la gmap ouverte 
	var map; // Objet gmap 


	function init_tout_lieu()
	{
		$.each(tab_lieu, function(i, e){
			e.action=0; 
		});
	}

	function maj_tab_theme()
	{
		tab_theme = []; 

		$('#liste_theme input[name="tab_theme[]"]:checked').each(function(){
			tab_theme.push( parseInt( $(this).val() ) ); 
		});
	}

	function maj_type_rem()
	{
		tab_type_rem = []; 

		$('#liste_theme input[name="tab_type_rem[]"]:checked').each(function(){
			tab_type_rem.push( parseInt( $(this).val() ) ); 
		});
	}

	function onglet_retour()
	{
		if( $('#liste_selection').is(':visible') )
		{
			onglet_active('prop'); 
		}
	}

	function onglet_active(ong)
	{
		if( ong == 'prop')
		{
			$('#prop').css('background', 'white'); 
			$('#sel, #lr').css('background', 'rgba(255,255,255,0.5)'); 
			$('#liste_selection, #onglet_lr').hide(); 
			$('#onglet_prop').show(); 
		}
		else if( ong == 'lr' )
		{
			$('#prop, #sel').css('background', 'rgba(255,255,255,0.5)'); 
			$('#lr').css('background', 'white'); 
			$('#onglet_lr').show(); 
			$('#onglet_prop, #liste_selection').hide(); 
		}
		else
		{
			$('#prop, #lr').css('background', 'rgba(255,255,255,0.5)'); 
			$('#sel').css('background', 'white'); 
			$('#liste_selection').show(); 
			$('#onglet_prop, #onglet_lr').hide(); 
		}
	}

	function prop_id(id)
	{
		return evenement_par_id(id, lsevent); 
	}

	function sel_id(id)
	{
		return evenement_par_id(id, lsel); 
	}

	function evenement_par_id(id, tab)
	{
		var i; 

		for(i=0; i<tab.length; i++)
		{
			if( tab[i].id == id)
			{
				return tab[i]; 
			}
		}

		return false; 
	}

	function ajt_rem(rem)
	{
		if( rem.long != 0 && rem.lat != 0 )
		{
			tab_rem.push({
				'lat' : rem.lat,
				'long' : rem.long, 
				'id' : rem.id, 
				'titre' : rem.titre
			});
		}

		$('#liste_lr').append(rem.html); 
	}

	function vide_tout_rem()
	{
		for(var ind in tab_tout_rem)
		{
			var rem = tab_tout_rem[ind]; 

			if( rem.marker )
			{
				debug('vidée'); 
				rem.marker.setMap(null); 
			}
			else
			{
				debug('Non vidée'); 
			}
		}

		tab_tout_rem = []; 
	}

	function vide_rem()
	{
		tab_rem = []; 
		$('#liste_lr').empty(); 
		maj_nb_rem(0); 
	}

	function maj_nb_rem(nb)
	{
		$('#lr_nb').text(nb); 
	}

	function jour(d)
	{
		var d = d.split('-'); 
		var a = parseInt(d[0]);
		var m = parseInt(d[1]); 
		var j = parseInt(d[2]);
		var $jr = $('[data-annee='+a+'][data-mois='+m+'] .jour').filter(function(){ return $('.num', this).text() == j; });
		return $jr; 
	}

	// Intervertit deux élément html
	// Thx Stack Overflow
	function swapElements(siblings, subjectIndex, objectIndex) 
	{
		// Get subject jQuery
		var subject = $(siblings.get(subjectIndex));
		// Get object element
		var object = siblings.get(objectIndex);
		// Insert subject after object
		subject.insertAfter(object);
	}

	// Place les numéros des événements sélectionnés dans le calendrier 
	function cal_sel()
	{
		lsel.sort(function(a,b){
			var da=0, db=0; 

			for(var ia in a.date_sel)
			{
				if( a.date_sel[ia] )
				{
					da = ia; 
					break; 
				}
			}

			for( var ib in b.date_sel )
			{
				if( b.date_sel[ib] )
				{
					db = ib; 
					break; 
				}
			}

			return db == da ? 0 : (da < db ? -1 : 1); 
		});

		$('.nbs').text(''); 

		$.each(lsel, function(ind, e){
			var $ev = $('#liste_selection div.evenement[data-id='+e.id+']'); 
			ev_mod_num(e, ind+1); 

			if( $ev.index() != ind )
			{
				swapElements($('#liste_selection .evenement'), $ev.index(), ind); 
			}

			$('.event_num', $ev).text(ind+1); 

			for( var ed in e.date_sel )
			{
				if( e.date_sel[ed] )
				{
					var $jr = jour(ed); 

					if( $jr )
					{
						$('.nbs', $jr).append('<span class="selection sel'+(ind+1)+'" >'+(ind+1)+'</span>'); 
					}
				}
			}
		});
	}

	function ev_mod_num(e, num)
	{
		var $ev = $('#liste_selection div.evenement[data-id='+e.id+']'); 
		e.num = num; 
		$('sup', $ev).text(num); 
		$ev.attr('data-num', num);
	}

	// Ajoute un événement à la liste de sélection
	/*
	function ajt_sel(ev)
	{
		if( sel_id(ev.id) )
		{
			debug('Imposibru'); 
			return; 
		}

		var nb = lsel.push(ev); 

		ev.date_sel = {}; 

		$.each(ev.date, function(i, e){
			ev.date_sel[e] = true; 
		}); 

		ev.num = nb; 
		var e = ev_html_sel(ev); 

		$('#sel_nb').text(lsel.length); 
		$('#liste_selection').append(e); 	

		cal_sel(); 

		$.each(ev.date, function(i, e){
			jour_nbe_ajt(e, -1); 
		});

		maj_tab_id(); 
		maj_ev_map_sel(ev, true); 
	}
	*/

	function maj_map_sel()
	{
		if(!map) return; 

		$.each(lsel, function(i,ev){
			maj_ev_map_sel(ev, false); 
		});
	}

	function maj_ev_map_sel(ev, retire_nbe)
	{
		if(!map) return; 

		$.each(ev.lieu, function(i, e){
			var idl = e.id; 
			var j=0; 
			var lieu = tab_lieu[idl];

			lieu.marker.setIcon('http://maps.google.com/mapfiles/ms/icons/orange-dot.png');

			if( !lieu.marker.getMap() )
			{
				lieu.marker.setMap(map); 
			}

			if( retire_nbe )
			{
				lieu.nbe--; 
			}
			lieu.nbs++; 
			maj_info_bull(lieu); 
		}); 
	}

	function nb_prop_ajt(nb)
	{
		var n = parseInt($('#prop_nb').text() )+nb;
		$('#prop_nb').text(n); 
		nb_prop = n; 
	}

	function nb_prop_mut(nb)
	{
		$('#prop_nb').text(nb); 
		nb_prop = nb; 
	}

	// Retire un événement de la liste de sélection
	function retire_sel(eid)
	{
		var $ev = $('#liste_selection .evenement[data-id='+eid+']'); 
		var ev; 
		var i=0; 

		for( i=0; i<lsel.length; i++)
		{
			if( eid == lsel[i].id )
			{
				ev = lsel[i]; 
				lsel.splice(i,1); 
				break; 
			}
		}

		$('#sel_nb').text(lsel.length); 
		var $evc = $ev.clone(); 
		$ev.remove(); 
		cal_sel(); 
		maj_tab_id(); 	

		var fd = ev_filtre_date(ev); 
		var ft = ev_filtre_theme(ev); 
		var fl = ev_filtre_lieu(ev);

		if( fd && ft && fl)
		{
			// Maj ls prop
			$('#liste_proposition').append($evc); 
			nb_prop_ajt(1); 
		}

		if( ft && fl )
		{
			// Maj calendrier 
			$.each(ev.date, function(i,e){
				jour_nbe_ajt(e, 1); 
			});
		}

		$.each(ev.lieu, function(i, e) {
			var l = lieu_id(e.id); 

			if( fd && fl )
			{
				l.nbe++; 
			}

			l.nbs--;

			if( l.nbs <= 0 && l.nbe>0 )
			{
				l.marker.setIcon('http://maps.google.com/mapfiles/marker_white.png'); 
			}
			else if( l.nbs <= 0 && l.nbe <= 0 )
			{
				l.marker.setMap(null); 
			}

			maj_info_bull(l); 
		});
	}

	function jour_nbe_ajt(d,nb)
	{
		var $jr = jour(d); 
		var nbe = $('.nbe', $jr).text(); 
		nbe = nbe ?  parseInt(nbe) : 0; 
		var res = nbe+nb;
		if( res == 0 )
		{
			$('.nbe', $jr).text(''); 
		}
		else
		{
			$('.nbe', $jr).text(res); 
		}
	}

	// Renvoi l'événement sous forme html
	/*
	function ev_html_prop(ev)
	{
		return ev_html(ev, true); 
	}

	function ev_html_sel(ev)
	{
		return ev_html(ev, false); 
	}
	*/

	function ev_premiere_date_sel(ev)
	{
		var i, v; 

		if( !date1 && !date2 )
		{
			return ev.date[0]; 
		}

		for(i=0; i<ev.date.length; i++)
		{
			if( date_inter(ev.date[i], date1, date2) )
			{
				return ev.date[i]; 
			}
		}

		return ev.date[0];
	}

	$('#liste_proposition').on('click', '.voir_date', function(){

		if( $('#annule_montre_date') )
		{
			$('#annule_montre_date').click(); 
		}

		var ev = JSON.parse( $(this).closest('.evenement').attr('data-event') ); 

		$('#cal_menu_bt').append( $('<a id="annule_montre_date" class="bouton_menu" >Ne plus montrer les dates</a>').click(function(){
			$.each(ev.date, function(i, e){
				var $jr = jour(e); 				

				$('.jour_sep', $jr).css('background', 'none');
			});

			$(this).remove(); 
			cal_redui(); 
		}));

		var func = function(){
			$.each(ev.date, function(i, e){
				var $jr = jour(e); 				
				$('.jour_sep', $jr).css('background', 'yellow');
			});
		};

		cal_etendre(func); 
	});

	$('#liste_selection').on('click', ".gestion_date", function(){

		if( $('#cal_menu_valide') )
		{
			$('#cal_menu_valide').click(); 
		}

		var ev = sel_id($(this).closest('.evenement').attr('data-id') ); 

		var num = $(this).closest('.evenement').attr('data-num'); 		

		var func = function(){
			$('#cal_menu_bt').append( $('<a class="bouton_menu date_masquer" >Masquer toutes les dates <span class="rond_rouge" ></span></a>').click(function(){
				$('.sel'+num).removeClass('date-ajoute'); 
				$('.sel'+num).addClass('date-retire'); 
			}));

			$('#cal_menu_bt').append( $('<a class="bouton_menu date_afficher" >Afficher toutes les dates <span class="rond_vert" ></span></a>').click(function(){
				$('.sel'+num).removeClass('date-retire'); 
				$('.sel'+num).addClass('date-ajoute'); 
			}));

			$('#cal_menu_bt').append( $('<a id="cal_menu_valide" class="bouton_menu date_valider" >Valider le planning</a>').click(function(){
				$('.sel'+num).each(function(){
					var d = j2date( $(this).closest('.jour') );
					ev.date_sel[d] = $(this).hasClass('date-ajoute'); 
				});

				$('.date-retire').remove(); 
				$('.date-ajoute').removeClass('date-ajoute'); 
				$('.sel'+num).unbind('click'); 
				$('#cal_menu_bt').empty(); 
				cal_sel(); 
			})); 
			
			$.each(ev.date, function(i, e){
				var $jr = jour(e); 				

				if( $('.sel'+num, $jr ).length == 0 )
				{
					$('.nbs', $jr).append('<span class="selection sel'+num+' date-retire" >'+num+'</span>'); 
				}
				else
				{
					$('.nbs .sel'+num, $jr).addClass('date-ajoute'); 
				}

				$('.sel'+num, $jr).click(function(){
					$(this).toggleClass('date-ajoute'); 
					$(this).toggleClass('date-retire'); 
				});
			});
		}; 

		cal_etendre(func); 
		return false; 
	});

	$('#liste_selection').on('click', '.bt_retire', function (){
		var eid = $(this).closest('.evenement').attr('data-id'); 
		retire_sel(eid); 
		return false; 
	}); 

	function j2date($jr)
	{
		var j = $('.bt_jr .num', $jr).text(); 
		var m = $jr.closest('.mois').attr('data-mois'); 
		var a = $jr.closest('.mois').attr('data-annee'); 
		if( j < 10 )
		{
			j = '0'+j; 
		}

		if( m < 10 )
		{
			m = '0'+m; 
		}

		return a+'-'+m+'-'+j; 
	}

	function maj_tab_id()
	{
		tabid = []; 
		var i=0; 
		for(i=0; i<lsel.length; i++)
		{
			tabid.push(lsel[i].id); 
		}
	}

	function pinSymbol(color) {
	    return {
		path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0',
		fillColor: color,
		fillOpacity: 1,
		strokeColor: '#000',
		strokeWeight: 2,
		scale: 1,
	   };
	}

	function maj_tab_lieu(ntab_lieu)
	{
		var ftable={}; 

		for( var id_lieu in ntab_lieu )
		{
			if( tab_lieu[id_lieu] )
			{
				ftable[id_lieu] = tab_lieu[id_lieu];
				ftable[id_lieu].nbe = ntab_lieu[id_lieu].nbe; 
			}
			else
			{
				ftable[id_lieu] = ntab_lieu[id_lieu]; 
			}
		}

		$.each(lsel, function(i, e){
			$.each(e.lieu, function(il, el){
				if( !ftable[el.id] )
				{
					ftable[el.id] = tab_lieu[el.id]; 
				}
			});
		});

		for(var id_lieu in tab_lieu )
		{
			if( !ftable[id_lieu] )
			{
				tab_lieu[id_lieu].marker.setMap(null);
			}
		}

		delete tab_lieu; 
		tab_lieu = ftable; 
	}

	function maj_tab_tout_rem(ntab_tout_rem)
	{
		var ftable = {}; 

		for( var idr in ntab_tout_rem)
		{
			if( tab_tout_rem[idr] )
			{
				ftable[idr] = tab_tout_rem[idr]; 
			}
			else
			{
				ftable[idr] = ntab_tout_rem[idr]; 
			}
		}

		// Ajouter dans ftable les rem présente dans la séléction mais pas la jeu de résultat 

		// ---- 

		for( var idr in tab_tout_rem )
		{
			if( !ftable[idr] )
			{
				tab_tout_rem[idr].marker.setMap(null); 
			}
		}
		
		delete tab_tout_rem; 
		tab_tout_rem = ftable; 	
	}

	function maj_map()
	{
		if( !map ) return; 

		init_tout_lieu(); 
		var i=0,j; 
		var lieu;
		var ls_marker = []; 
		var ls_add_marker = []; 

		for(var id_lieu in tab_lieu )
		{
			lieu = tab_lieu[id_lieu]; 

			lieu.nbs = 0; 

			if( !lieu.marker )
			{
				lieu.marker = new google.maps.Marker({
					position:new google.maps.LatLng(lieu.lat, lieu.long ), 
					map:null,
					animation: google.maps.Animation.DROP,
					title:lieu.value,
					icon : 'http://maps.google.com/mapfiles/marker_white.png'
				}); 
			}

			lieu.action=1; 
		}

		$.each(lsel, function(i, e){
			$.each(e.lieu, function(il, el){
				tab_lieu[el.id].action=2; 
				tab_lieu[el.id].nbs++; 
			});
		});

		for(var idl in tab_lieu)
		{
			var lieu = tab_lieu[idl]; 

			if( lieu.action == 0 )
			{
				if( lieu.marker )
				{
					lieu.marker.setMap(null); 
				}
			}
			else 
			{
				if( lieu.action == 1 )
				{
					lieu.marker.setIcon('http://maps.google.com/mapfiles/marker_white.png');
				}
				else if( lieu.action == 2 )
				{
					lieu.marker.setIcon('http://maps.google.com/mapfiles/ms/icons/orange-dot.png'); 					
				}

				if( !lieu.marker.getMap() && (lieu.lat!=0 && lieu.long!=0 ) )
				{
					ls_marker.push(lieu.marker); 
				}

				maj_info_bull(lieu); 
			}
		}

		if( tab_tout_rem )
		{
			for( var ind in tab_tout_rem )
			{
				var rem = tab_tout_rem[ind]; 

				if( !rem.marker)
				{
					rem.marker = new google.maps.Marker({
						position:new google.maps.LatLng(rem.lat, rem.long ), 
						map:null,
						animation: google.maps.Animation.DROP,
						title: rem.titre,
						icon : 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
					}); 

					ls_marker.push(rem.marker); 
				}

				maj_info_bull_rem(rem); 
			}
		}
		
		if( ls_marker.length > 0 )
		{
			// Thx Stack Overflow
			var bounds = ls_marker.reduce(function(bounds, marker) {
				return bounds.extend(marker.getPosition());
			}, new google.maps.LatLngBounds());

			map.panTo(bounds.getCenter());

			(function(){
				var i=0; 
				var delay_maj_map = function(){
					if( i < ls_marker.length )
					{
						ls_marker[i].setAnimation(google.maps.Animation.DROP); 
						ls_marker[i].setMap(map); 		
						i++; 
						timeout_map = setTimeout(delay_maj_map,30); 
					}
				}; 
				delay_maj_map(); 
			})(); 
		}
	}

	function maj_info_bull(lieu)
	{
		if(!map) return; 

		if( !lieu.infoWindow )
		{
			lieu.infoWindow = new google.maps.InfoWindow();
			lieu.marker.addListener('click', function(){
				while(iw_open.length>0)
				{
					iw_open.pop().close(); 
				}
				lieu.infoWindow.open(lieu.marker.get('map'), lieu.marker); 
				iw_open.push(lieu.infoWindow); 
			});
		}
		
		var $bl = $('<div><p><strong>'+lieu.value+'</strong> <a href="#" >Filtrer par ce lieu</a></p>'
			+'<p>Recherche : '+lieu.nbe+' événement(s).</p>'
			+'<p>Sélection : '+lieu.nbs+' événement(s).</p>'
			+'</div>');
		$('a', $bl).click(function(){
			mut_flieu(lieu.id, lieu.value); 
			return false; 
		});

		lieu.infoWindow.setContent($bl[0]); 
	}

	function maj_info_bull_rem(rem)
	{
		if( !map) return;

		if( !rem.infoWindow )
		{
			rem.infoWindow = new google.maps.InfoWindow();
			rem.marker.addListener('click', function(){
				while(iw_open.length>0)
				{
					iw_open.pop().close(); 
				}
				rem.infoWindow.open(rem.marker.get('map'), rem.marker); 
				iw_open.push(rem.infoWindow); 
			});
		}
		
		var $bl = $('<div><div style="float:left;margin-right:2px" >'+rem.type_html+'</div>'
			+'<div style="margin-left:35px" ><strong>'+rem.titre+'</strong><div>'+rem.desc+'</div></div></div>');

		rem.infoWindow.setContent($bl[0]); 
	}

	function initialize_map()
	{       
		var myLatLng = new google.maps.LatLng(45.736913, 1.737173);
		var myOptions = { 
			center: myLatLng,
			zoom : 8, 
			mapTypeId : google.maps.MapTypeId.ROADMAP
		}

		map = new google.maps.Map(document.getElementById('map'), myOptions ); 
	}

	function lieu_id(id)
	{
		if( !id ) return false; 

		return tab_lieu[id] ? tab_lieu[id] : false; 
	}

	function date_fr(d)
	{
		var td = d.split('-'); 
		var a=td[0],m=td[1], j=td[2]; 
		return j+' '+nom_mois[m-1]+' '+a; 
	}

	function date_fr_ev(d)
	{
		var od = new Date(); 
		var td = d.split('-'); 
		var a=parseInt(td[0]),m=parseInt(td[1]), j=parseInt(td[2]); 

		od.setFullYear(a,m-1,j); 
		var mois = nom_mois[m-1];
		var nj = nom_jour[od.getDay()];
		return nj+' '+j+' '+mois+' '+a; 
	}

	function tout_theme_sel()
	{
		return tab_theme.length == $('#liste_theme input[name="tab_theme[]"]').length; 
	}

	function tout_type_rem_sel()
	{
		return tab_type_rem.length == $('#liste_theme input[name="tab_type_rem[]"]').length; 
	}

	function barre_filtre()
	{
		var filtre=[]; 
		var $bf = $('#barre_filtre'); 
	
		if( date1 && date2 )
		{
			if( date_cmp(date1, date2) == 0 )
			{
				var dtxt = 'Le '+date_fr(date1);
			}
			else
			{
				var dtxt = 'Du '+date_fr(date1)+' au '+date_fr(date2); 
			}

			filtre.push($('<span>'+dtxt+'</span>').append(' ').append( $('<a>X</a>').click(function(){
				$('.dsel').removeClass('dsel'); 
				date1 = null; 
				date2 = null; 
				$(this).remove(); 
				recup(RECUP_TOUT); 
			}))); 
		}

		if( !tout_theme_sel() )
		{
			var text_theme; 

			// Afficher le nom du/des thèmes si un ou deux seulement sont coché
			// ----- 

			text_theme = $('<span>'+tab_theme.length+' thèmes</span>'); 

			filtre.push( text_theme.append(' ').append( $('<a>X</a>').click(function(){
				theme_tout_coche(); 
			}))); 
		}

		if( filtre_lieu )
		{
			var txt; 

			if( rayon )
			{
				txt = 'à '+rayon+'km autour de '+filtre_lieu;
			}
			else
			{
				txt = 'à '+filtre_lieu; 
			}

			filtre.push($('<span>'+txt+'</span>').append(' ').append( $('<a>X</a>').click(function(){
				lieu_reset(); 
			}))); 
		}

		$bf.empty();
		$bf.append('Filtres : ').append(filtre[0]);
		
		for(i=1; i<filtre.length; i++)
		{
			$bf.append(', '); 
			$bf.append(filtre[i]);
		}
	}

	function barre_filtre_rem()
	{
		var filtre=[]; 
		var $bf = $('#barre_filtre_rem'); 
	
		if( filtre_lieu )
		{
			var txt; 

			if( rayon )
			{
				txt = 'à '+rayon+'km autour de '+filtre_lieu;
			}
			else
			{
				txt = 'à '+filtre_lieu; 
			}

			filtre.push($('<span>'+txt+'</span>').append(' ').append( $('<a>X</a>').click(function(){
				lieu_reset(); 
			}))); 
		}

		if( !tout_type_rem_sel() )
		{
			filtre.push( $('<span>'+tab_type_rem.length+' types</span>').append(' ').append( $('<a>X</a>').click(function(){
				type_rem_tout_coche(); 
			}))); 
		}

		$bf.empty();
		$bf.append('Filtres : ').append(filtre[0]);
		
		for(i=1; i<filtre.length; i++)
		{
			$bf.append(', '); 
			$bf.append(filtre[i]);
		}
	}

	var $ch = null; 
	var timeout_cal = null;
	var timeout_map = null; 

	$('.pagin-prop').on('click', 'a', function(){
		num_page = $(this).attr('data-num'); 
		recup(RECUP_EVENT); 
		return false; 
	});

	$('#liste_proposition').on('click', '.bt_ajouter', function(){
		var $ev = $(this).closest('.evenement'); 
		var ev = JSON.parse( $ev.attr('data-event') ); 

		if( sel_id(ev.id) )
		{
			debug('Imposibru'); 
			return; 
		}

		$ev.remove(); 

		var nb = lsel.push(ev); 

		ev.date_sel = {}; 

		$.each(ev.date, function(i, e){
			ev.date_sel[e] = true; 
		}); 

		var $evc = $ev.clone(); 
		$('#liste_selection').append($evc); 

		$('#sel_nb').text(lsel.length); 

		cal_sel(); 

		$.each(ev.date, function(i, e){
			jour_nbe_ajt(e, -1); 
		});

		maj_tab_id(); 
		maj_ev_map_sel(ev, true); 

		return false; 
	});

	function recup(tout)
	{
		if( !$ch )
		{
			$ch = $('<span>Chargement...</span>');
		}

		if( timeout_cal )
		{
			clearTimeout(timeout_cal);
			timeout_cal = null; 
		}

		if( timeout_map ) 
		{
			clearTimeout(timeout_map);
			timeout_map = null; 
		}

		$('#pied_prop').append( $ch ); 

		if( tout == RECUP_TOUT )
		{
			vide_ls_prop(); 
			vide_rem(); 
			num_page = 1; 
			num_page_rem = 0; 
			barre_filtre(); 	
			barre_filtre_rem(); 
			tabid_img = []; 
			for(var i in tabid)
			{
				tabid_img.push(tabid[i]); 
			}
		}

		var param = {
			'datedu': date1,
			'dateau': date2,
			'idt' : tab_theme.join(','), 
			'idtr' : tab_type_rem.join(','), 
			'evi' : tout==RECUP_TOUT ? tabid.join(',') : tabid_img.join(','), 
			'idl' : flieu,
			'ray' : rayon, 
			'tt' : tout,
			'np' : num_page,
			'npr' : num_page_rem, 
			'x' : 1
		};

		$.ajax('<?php echo ADD_SITE_DYN ?>index.php',{
			'type': 'POST', 
			'data' : param,
			'async' : true,
			'success' : function(data) {

			if( data.tab_date )
			{
				var $jour = $('.jour'); 
				var nbjr = $jour.length; 	
				var ind_jr = 0; 

				var maj_cal = function(){
					if( ind_jr < nbjr )
					{
						var $jr = $jour.eq(ind_jr); 
						ind_jr++; 
						var j = parseInt($('.num',$jr).text()); 
						if( j <10 ) j = '0'+j; 
						var m = parseInt( $jr.closest('.mois').attr('data-mois') ); 
						if( m<10 ) m = '0'+m; 
						var a = $jr.closest('.mois').attr('data-annee'); 
						
						if(  data.tab_date[ a+'-'+m+'-'+j] != parseInt($('.nbe',$jr).text() ) )
						{
							$('.nbe',$jr).animate({'opacity':0}, 500, 'linear', function(){
								if(  data.tab_date[ a+'-'+m+'-'+j] )
								{
									$('.nbe', $jr).text(data.tab_date[a+'-'+m+'-'+j]);
								}
								else
								{
									$('.nbe', $jr).text('');
								}
								$('.nbe', $jr).animate({'opacity':1},500, 'linear'); 
							});

							timeout_cal = setTimeout(maj_cal, 25); 
						}
						else
						{
							maj_cal(); 
						}
					}
				};
				maj_cal(); 
			}
			
			if( data.liste_rem )
			{
				if( data.liste_rem.length == 0 && data.lr_mess )
				{
					$('#liste_lr').append('<div>'+data.lr_mess+'</div>'); 
				}
				else
				{
					for(var rem in data.liste_rem)
					{
						ajt_rem(data.liste_rem[rem]); 
					}
				}
			}

			if( data.tab_rem )
			{
				maj_tab_tout_rem(data.tab_rem); 
			}

			if( data.tab_lieu )
			{
				maj_tab_lieu(data.tab_lieu); 
			}

			if( tout == RECUP_TOUT )
			{
				nb_prop_mut(data.nbe); 
				maj_nb_rem(data.nbr); 
				nb_page_recup = parseInt(data.nbe/20); 
				nb_page_recup_rem = parseInt(data.nbr/20); 
			}

			if( data.liste_evenement )
			{
				$('#liste_proposition').html(data.liste_evenement).ready(function(){
					$('.pagin-prop').html(data.pagin_event).ready(function(){
						if( recup == RECUP_EVENT )
						{
							$('.pagin-prop')[0].scrollIntoView();
						}
					}); 
				}); 
			}

			if( nb_page_recup_rem == num_page_rem )
			{
				$('#bt_plus_lr').hide(); 
			}
			else
			{
				$('#bt_plus_lr').show(); 
			}

			if( nb_page_recup == num_page )
			{
				$('#bt_plus_ev').hide(); 
			}
			else
			{
				$('#bt_plus_ev').show(); 
			}

			maj_map(); 
			debug(data.debug); 

			$ch.remove(); 
		}}).fail(function(xhr, status, error){
			debug("An AJAX error occured: " + status + "\nError: " + error);	
			$ch.remove(); 
		}); 
	};

	function vide_marker(tab_ignore)
	{
		var i, j,ajt; 

		if(!tab_ignore)
		{
			tab_ignore = []; 
		}

		$.each(lsel, function(i, e) {
			$.each(e.lieu, function(il, el){
				tab_ignore.push(tab_lieu[el.id].marker); 
			});
		});
		
		for(var idl in tab_lieu)
		{
			var lieu = tab_lieu[idl]; 
			ajt=true; 

			if( tab_ignore )
			{
				for(j=0; j<tab_ignore.length; j++)
				{
					if(tab_ignore[j] == lieu.marker )
					{
						ajt=false;
						break; 
					}
				}
			}

			if( ajt && lieu.marker) 
			{
				lieu.marker.setMap(null); 
				delete tab_lieu[idl]; 
			}
		}
	}

	function vide_ls_prop()
	{
		lsevent = []; 
		$('#liste_proposition').text(''); 
		$('#bt_plus_ev').hide(); 
		$('#bt_plus_lr').hide(); 
		$('.barre_filtre').empty(); 
		$('.pagin-prop').empty(); 
	}

	function vide_lieu()
	{
		if( timeout_map ) 
		{
			clearTimeout(timeout_map);
			timeout_map = null; 
		}

		vide_marker(); 
	}

	function lieu_reset()
	{
		flieu = null; 
		filtre_lieu = ''; 
		rayon = null; 
		$('#rayon').prop('selectedIndex',0);
		$('#ch_lieu').val(''); 
		$('#bt_sup_lieu').remove(); 
		onglet_retour(); 
		recup(RECUP_TOUT); 
	}

	/*
		d1 > d2 : 1 
		d1 < d2 : -1
		d1 == d2 : 0 
	*/
	function date_cmp(d1, d2)
	{
		var td1 = d1.split('-'), td2=d2.split('-');
		var a1 = parseInt(td1[0]), a2 = parseInt(td2[0]); 
		var m1 = parseInt(td1[1]), m2 = parseInt(td2[1]); 
		var j1 = parseInt(td1[2]), j2 = parseInt(td2[2]); 

		if( a1==a2 )
		{
			if( m1==m2 )
			{
				if( j1==j2 )
				{
					return 0; 
				}
				else
				{
					return j1 > j2 ? 1 : -1; 
				}
			}
			else
			{
				return m1 > m2 ? 1 : -1; 
			}
		}
		else
		{
			return a1 > a2 ? 1 : -1; 	
		}
	}

	function ev_filtre_date(ev)
	{

		var i, v; 

		if( !date1 && !date2 )
		{
			return true; 
		}

		for(i=0; i<ev.date.length; i++)
		{
			if( date_inter(ev.date[i], date1, date2) )
			{
				return true; 
			}
		}

		return false; 
	}

	function date_inter(dc, d1, d2)
	{
		var r1 = date_cmp(dc, d1); 
		var r2 = date_cmp(dc, d2); 
		return r1==0 || r2 == 0 || (r1==1 && r2 == -1); 
	}

	function distance(lat1, lon1, lat2, lon2) 
	{
		var R = 6371; // Radius of the earth in km
		var dLat = (lat2 - lat1) * Math.PI / 180;  // deg2rad below
		var dLon = (lon2 - lon1) * Math.PI / 180;
		var a = 0.5 - Math.cos(dLat)/2 
			+ Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) 
			* (1 - Math.cos(dLon))/2;

		return R * 2 * Math.asin(Math.sqrt(a));
	}

	function lieu_dans_rayon(c, r, lieu)
	{
		var d = distance(c.lat, c.long, lieu.lat, lieu.long);  
		return d < r; 
	}

	function ev_filtre_lieu(ev)
	{
		var i, lieu;

		if( !flieu)
		{
			return true; 
		}

		if(rayon)
		{
			lieu = lieu_id(flieu); 
		}
		
		for( i=0; i<ev.lieu.length; i++)
		{
			var evlieu = lieu_id(ev.lieu[i].id); 

			if( flieu == ev.lieu[i].id || (rayon && lieu_dans_rayon(lieu, rayon, evlieu) ) )
			{
				return true; 
			}
		}

		return false;
	}

	function ev_filtre_theme(ev)
	{
		var i; 

		if( tout_theme_sel() )
		{
			return true; 
		}

		for(i=0; i< tab_theme.length; i++)
		{
			if( tab_theme[i] == ev.categorie.groupe)
			{
				return true; 
			}
		}

		return false; 
	}

	function mut_flieu(idl, nom)
	{
		flieu = idl; 
		filtre_lieu = nom; 
		$('#ch_lieu').val(nom);
		$('#ch_lieu').blur(); 
		onglet_retour(); 
		recup(RECUP_TOUT); 
	}
	
	var cal_redui_top; 
	
	function cal_etendre(fct)
	{
		if( !$('#bl_cal').hasClass('bl_cal_etendu') )
		{
			cal_redui_top = $('#cal_mois').scrollTop(); 
			$('#bl_cal').css({'display' : ''}); 

			if( $(window).width() > BREAKPOINT)
			{
				var width = 650; 

				$('#bl_cal').animate({'width':width}, 500); 

				$('#cal_mois').animate({'opacity':0}, 250, function(){

					$('.mois').css('float', 'left'); 
					$('#bl_cal').addClass('bl_cal_etendu'); 
					$('#bl_cal').removeClass('bl_cal_redui'); 

					if( fct)
					{
						fct(); 
					}

					$('#cal_mois').width(width+'px'); 
					$('#cal_mois').height('auto'); 

					var menu_decal = $('#cal_menu').height(); 

					var height = menu_decal+$('#cal_mois').height(); 

					if( height > $(window).height() )
					{
						height = $(window).height()-menu_decal; 
					}

					$('#cal_mois').animate({'opacity':1}, 250); 
					$('#bl_cal').animate({'height':height}, {
						'duration' : 250, 
						'queue' : false,
						'complete' : function(){
							$('#cal_mois').height('auto'); 
						}
					}); 

				});
			}
			else
			{
				var width = 185; 

				$('#bl_cal').css({'display':'block'}); 
				var menu_decal = $('#cal_menu').height(); 
				$('#cal_mois').css({'width' : width+'px', 'height':'auto'}); 
				var height = menu_decal+$('#cal_mois').height(); 

					h_bl_cal = $(window).height()-20; 
					h_cal_mois = h_bl_cal - menu_decal-20; 

				$('#cal_mois').css({'width' : width+'px', 'height':h_cal_mois+'px'}); 
				$('#bl_cal').removeClass('bl_cal_redui'); 
				$('#bl_cal').addClass('bl_cal_etendu'); 
				$('#bl_cal').css({
					'width':width+"px", 
					'height':h_bl_cal+"px", 
					'opacity':1
				}); 
				$('#bl_cal').css({'display':''}); 

				if( fct)
				{
					fct(); 
				}
			}

			$('#cal_mode img').attr('src', 'http://info-limousin.com/img/planning/bouton_fermeture.png'); 
		}
		else if( fct )
		{
			fct(); 
		}
	}

	function cal_redui()
	{
		if( !$('#bl_cal').hasClass('bl_cal_redui') )
		{
			if( $('#cal_menu_valide') )
			{
				$('#cal_menu_valide').click(); 
			}

			if( $(window).width() > BREAKPOINT )
			{
				
				$('#cal_mois').animate({'opacity':0, 'height' : $(window).height()-90 }, 250, function(){
					$('#bl_cal').removeClass('bl_cal_etendu'); 
					$('#bl_cal').addClass('bl_cal_redui'); 

					$('#cal_mois').width('auto'); 
					$('.mois').css('float', 'none'); 
					$('#cal_mois').scrollTop(cal_redui_top); 
					$('#cal_mois').animate({'opacity':1}, 250); 
				});

				$('#bl_cal').animate({'height':$(window).height()-60, 'width':185}, 500); 
			}
			else
			{
				$('#cal_mois').css({'opacity':0, 'height' : $(window).height()-65 }); 
				$('#bl_cal').removeClass('bl_cal_etendu'); 
				$('#bl_cal').addClass('bl_cal_redui'); 

				$('#cal_mois').width('auto'); 
				$('.mois').css('float', 'none'); 
				$('#cal_mois').scrollTop(cal_redui_top); 
				$('#cal_mois').animate({'opacity':1}, 250); 

				$('#bl_cal').css({'height':$(window).height()-60, 'width':185}); 

				$('#bl_cal').css({'display' : ''}); 
			}

			$('#cal_mode img').attr('src', 'http://info-limousin.com/img/planning/bouton_ouverture.png'); 
		}
	}

	function resize_bl_cal()
	{
		$('#bl_cal').animate({'height':$(window).height()-60}, 500); 
		$('#cal_mois').animate({'max-height': ($(window).height()-90)}, 500); 
	}

	function theme_tout_coche()
	{
		$('.theme input[name="tab_theme[]"').prop('checked', true); 
		maj_tab_theme(); 
		onglet_retour(); 
		recup(RECUP_TOUT);
	}

	function type_rem_tout_coche()
	{
		$('.theme input[name="tab_type_rem[]"').prop('checked', true); 
		maj_type_rem(); 
		onglet_retour(); 
		recup(RECUP_TOUT);
	}

	function ferme_aide()
	{
		if( aide_ouvert)
		{
			aide_ouvert.remove(); 
			tab_aide[num_aide].func_ferme(); 
			aide_ouvert = null; 
		}
	}

	function ouvre_aide(num)
	{
		ferme_aide(); 

		num_aide = num; 
		var a = tab_aide[num];
		var attach = $(a.ida); 
		var pos = $('<div class="fen-aide" >'); 
		pos.css({'position':'absolute', 'display':$(a.ida).css('display'),'width':'1000px' });
		var fen = $('<div>');
		var menu = $('<div>'); 
		menu.css({'text-align':'right'}); 
		var nav = $('<div>'); 
		nav.css({'text-align':'center'}); 

		if( num_aide > 0 )
		{
			nav.append($('<a>Précédent</a>').click(function(){
				ouvre_aide(num_aide-1); 
			})); 
			nav.append(' - ');
		}
		
		nav.append( (num+1)+'/'+tab_aide.length);

		if( num_aide < tab_aide.length-1)
		{
			nav.append(' - '); 
			nav.append($('<a>Suivant</a>').click(function(){
				ouvre_aide(num_aide+1); 
			})); 
		}

		menu.append($('<a>X</a>').click(function(){
			ferme_aide(); 
		}));

		var contenu = $('<div>'); 
		fen.css({'position':'absolute','background-color':'white','padding':'10px','z-index':999999, 'max-width':'500px',
			'box-shadow': '8px 8px 12px rgba(0,0,0,0.5)'});

		contenu.html(a.texte); 
		fen.append(menu);
		fen.append(contenu); 
		fen.append(nav); 
		pos.append(fen);

		if( a.in && a.in == true )
		{
			attach.prepend(pos);
		}
		else
		{
			attach.before(pos); 
		}

		var fen_left; 
		switch(a.pos)
		{
			case 'bottom' :
				fen.css({'top':attach.outerHeight()}); 
				fen_left = attach.outerWidth()/2 - fen.outerWidth()/2;
				fen.css({'left': fen_left }); 
			break;
			case 'top' : 
				fen.css({'top':-fen.outerHeight()+10});		
				fen_left = attach.outerWidth()/2 - fen.outerWidth()/2; 
				fen.css({'left': fen_left }); 
			break; 
			case 'left' : 
				fen_left = -fen.outerWidth(); 
				fen.css({'left':fen_left});		
				fen.css({'top':attach.outerHeight()/2 - fen.outerHeight()/2}); 
			break; 
		}

		fen.fadeIn(); 

		var fen_offset = fen.offset();
		var fixe_offset = $('body').offset(); 

		if( fen_offset.left < fixe_offset.left )
		{
			fen.css({'left' :fen_left+( fixe_offset.left - fen_offset.left) }); 	
		}

		a.func_ouvre(); 
		aide_ouvert = fen; 
	}

	onglet_active('prop'); 
	maj_tab_theme(); 
	maj_type_rem(); 
	initialize_map(); 
	init_tout_lieu(); 
	resize_bl_cal(); 

	$('#bt_plus_ev').hide(); 
	$('#bt_plus_lr').hide(); 
	$('#theme_tous_coche').click(function(){
		theme_tout_coche(); 
		type_rem_tout_coche(); 
	});

	$('#theme_tous_decoche').click(function(){
		$('.theme input').prop('checked', false); 
		$('.nbe').text(''); 
		vide_ls_prop(); 
		maj_tab_theme(); 
		maj_type_rem(); 
		vide_lieu(); 
		vide_tout_rem(); 
		maj_map(); 
		nb_prop_mut(0); 
	});

	$('.bt_jr').click(function(){
		return false; 
	});

	$('.bt_jr').mousedown(function(e){
		e.stopPropagation(); 	
		$('.dprov').removeClass('dprov'); 	
		date1 = j2date($(this).closest('.jour') );
		date2 = null; 
		return false; 
	});

	$('.bt_jr').mouseenter(function(e){

		if( date1 && !date2 )
		{
			$('.dprov').removeClass('dprov'); 	
			var dinter = j2date($(this).closest('.jour') );
			var d1 = date1 ; 

			if( date_cmp(date1, dinter) == 1 )
			{
				var tmp = d1; 
				d1 = dinter; 
				dinter = tmp; 
			}

			$('.jour').each(function(i, el){
				var $jr = $(el); 
				var date = j2date($jr); 	

				if( date_inter(date, d1, dinter) )
				{
					$('.bt_jr', $jr).addClass('dprov'); 
				}
			});
		}
	});

	$('.bt_jr').mouseup(function(e){
		e.stopPropagation(); 

		if( date1 )
		{
			$('.dprov').removeClass('dprov');
			$('.dsel').removeClass('dsel'); 	
			date2 = j2date($(this).closest('.jour') );

			if( date_cmp(date1, date2) == 1 )
			{
				var tmp = date1; 
				date1 = date2; 
				date2 = tmp; 
			}

			$('.jour').each(function(i, e){
				var $jr = $(e); 
				var date = j2date($jr); 	

				if( date_inter(date, date1, date2) )
				{
					$('.bt_jr', $jr).addClass('dsel'); 
				}
			});

			onglet_active('prop'); 
			recup(RECUP_TOUT); 
		}
		else
		{
			date1 = null;
			date2 = null; 
		}
		
		return false; 
	});

	$('.theme input[name="tab_theme[]"]').change(function(){
		maj_tab_theme(); 
		onglet_active('prop'); 
		recup(RECUP_TOUT); 
	}); 

	$('.theme input[name="tab_type_rem[]"]').change(function(){
		maj_type_rem(); 
		onglet_active('lr'); 
		recup(RECUP_TOUT); 
	});

	$('#ch_lieu').autocomplete({
		source : '../ajax/lieu-jq.php',
		minLength : 2, 
		select : function(event, ui){
			mut_flieu(ui.item['id'], ui.item['value']); 
		}
	});

	$('#rayon').change(function(){

		rayon = $(this).val(); 
		if( rayon == '0' )
		{
			rayon = null; 
		}

		if( flieu )
		{
			onglet_retour(); 
			recup(RECUP_TOUT); 
		}
	});


	$('#datedu').datepicker({
		dateFormat : "DD d MM y", 
		onClose : function(){
			var date = new Date($(this).datepicker('getDate') ); 
			date1 = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate(); 

			onglet_active('prop'); 
			recup(RECUP_TOUT); 
		}
	}); 

	$('#dateau').datepicker({
		dateFormat : "DD d MM y",
		onClose : function(){
			var date = new Date($(this).datepicker('getDate') ); 
			date2 = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate(); 

			onglet_active('prop'); 
			recup(RECUP_TOUT); 
		}
	}); 

	$('#prop').click(function(){
		onglet_active('prop'); 
	});

	$('#sel').click(function(){
		onglet_active('sel'); 
	});

	$('#lr').click(function(){
		onglet_active('lr'); 
	});

	$('#bt_plus_ev').click(function(){
		num_page++; 
		recup(RECUP_EVENT); 
	});

	$('#bt_plus_lr').click(function(){
		num_page_rem++; 
		recup(RECUP_REM); 
	});

	$('#cal_mode').click(function(){
		if( $('#bl_cal').hasClass('bl_cal_etendu') )
		{
			cal_redui(); 
		}
		else
		{
			cal_etendre(); 	
		}
	});

	(function(){
		
		var a=null; 

		$(window).resize(function(){
			if( a )
			{
				clearTimeout(a); 
			}

			a=setTimeout(resize_bl_cal, 500); 	
		});
	})(); 

	$('#planning').click(function(){
		$('#gen-planning').submit(); 
	});

	$('#gen-planning').submit(function(event){
		if( lsel.length == 0 )
		{
			alert("Vous devez avoir au moins un événement dans votre sélection.\nAstuce : cliquez sur le bouton aide en haut de la page pour suivre la visite guidée !"); 
			return false; 
		}

		var param={}; 

		$.each(lsel, function(i, e){
			param[e.id] = {};
			param[e.id].date = e.date_sel; 
			param[e.id].num = e.num; 
		}); 

		$('#planning-param').val( JSON.stringify(param) ); 

		return true;
	});

	var tab_aide = []; 
	var num_aide = 0; 
	var bordure_couleur = 'yellow'; 
	var bordure_couleur_defaut = '#a83e0e'; 
	var aide_ouvert = null; 
	var aide_contenu = <?php
	$phrase = rappel('dynagenda-phrase-aide');
	$tab = [];
	foreach($phrase as $id => list($int, $cont) )
	{
		$tab[$id] = $cont; 
	}
	echo json_encode($tab); 
	?>;

	tab_aide.push({'func_ouvre':function(){}, 'func_ferme': function(){},'texte':aide_contenu[0], 'ida':'#bt-aide', 'pos':'bottom'}); 
	tab_aide.push({'func_ouvre':function(){
			var $bld = $('#map'); 
			$bld.css({'border-color':bordure_couleur});	
			$('#sel_zone').css({'border-color':bordure_couleur}); 
		}, 
		'func_ferme': function(){
			var $bld = $('#map'); 
			$bld.css({'border-color':bordure_couleur_defaut});	
			$('#sel_zone').css({'border-color':bordure_couleur_defaut}); 
		}, 
		'texte':aide_contenu[1],
		'ida':'#bl_droite', 
		'pos':'bottom'
	}); 
	tab_aide.push({'func_ouvre':function(){
			var $bld = $('#bl_cal'); 
			$bld.css({'border-color':bordure_couleur});	
		}, 
		'func_ferme': function(){
			var $bld = $('#bl_cal'); 
			$bld.css({'border-color':bordure_couleur_defaut});	
		}, 
		'texte':aide_contenu[2],
		'ida':'#bl_cal', 
		'in' : true,
		'pos':'left'
	}); 
	tab_aide.push({'func_ouvre':function(){
			var $bld = $('#liste_theme'); 
			$bld.css({'border-color':bordure_couleur});	
		}, 
		'func_ferme': function(){
			var $bld = $('#liste_theme'); 
			$bld.css({'border-color':'transparent'});	
		}, 
		'texte':aide_contenu[3],
		'ida':'#liste_theme', 
		'pos':'top'
	}); 
	tab_aide.push({'func_ouvre':function(){
			var $bld = $('#liste'); 
			$bld.css({'border-color':bordure_couleur});	
			onglet_active('prop'); 
		}, 
		'func_ferme': function(){
			var $bld = $('#liste'); 
			$bld.css({'border-color':'transparent'});	
		}, 
		'texte':aide_contenu[4],
		'ida':'#liste', 
		'pos':'top'
	}); 
	tab_aide.push({'func_ouvre':function(){
			var $bld = $('#liste'); 
			$bld.css({'border-color':bordure_couleur});	
			onglet_active('sel'); 
		}, 
		'func_ferme': function(){
			var $bld = $('#liste'); 
			$bld.css({'border-color':'transparent'});	
		}, 
		'texte':aide_contenu[5],
		'ida':'#liste', 
		'pos':'top'
	}); 
	tab_aide.push({'func_ouvre':function(){
			var $bld = $('#bl_bt'); 
			$bld.css({'border-color':bordure_couleur});	
			cal_redui(); 
		}, 
		'func_ferme': function(){
			var $bld = $('#bl_bt'); 
			$bld.css({'border-color':'transparent'});	
		}, 
		'texte':aide_contenu[6],
		'ida':'#bl_bt', 
		'in' : true,
		'pos':'left'
	}); 

	$('#bt-aide').click(function(){
		ouvre_aide(num_aide); 	
	}); 

	tab_lieu = JSON.parse( $('#tab_lieu').text() ); 
	date1 = $('#date1').text(); 
	date2 = $('#date2').text(); 
	maj_map(); 
}); 
