<?php
require_once '../../include/init.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'ls_location_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'structure_class.php'; 

/*
	INIT 
*/

http_param( array('ids' => 0 ) ); 

if( !droit(GERER_UTILISATEUR ) )
{
	if(!str_droit_utilisateur($ids ) )
	{
		page_erreur(); 
	}
}

if( !($str = str_init($ids) ) ) 
{
	page_erreur(); 
}

/*
	TRAITEMENT 
*/

if( isset($_GET['code']) )
{
	// # Vérifier que le code appartient bien à la structure ??? 
	str_location_choix($str, $_GET['code']); 
}

/*
	AFFICHAGE 
*/

http_param( array('page' => 0, 'p' => 0 ) ) ;
$tab_page = array( C_ADMIN.'structure/str-liste.php', 'location-admin.php' ); 

$page_retour = isset($tab_page[ $page ] ) ? $tab_page[ $page ] : $tab_page[ 0 ]; 

$url_retour = $page_retour.'?p='.$p.'&amp;ids='.$ids ;

http_param(array('pl' => 0 ) ); 

$location = new ls_location; 
$location->page = $pl;
$location->structure = $ids; 
$loc = $location->requete(); 

$pagin = new pagin; 
$pagin->mut_nbp($loc->nb_page); 
$pagin->mut_url('location.php?pl=%p&amp;ids='.$ids); 
$pagin->mut_actif($pl); 

$dos = RETOUR.'jscalendar/';
ajt_style('calendar-win2k-cold-1.css', $dos);
ajt_script('calendar.js', $dos );  
ajt_script('calendar-en.js', $dos.'lang/' );  
ajt_script('calendar-fr.js', $dos.'lang/' );  
ajt_script('calendar-setup.js', $dos );

include HAUT_ADMIN 
?>

<h1>Liste des relais de <?php echo $str->acc_nom()  ?></h1>


<p><a href="location-form.php?ids=<?php echo $ids ?>" >Nouveau relais</a></p>


<p>Le lien "choisir" définit le relais qui accompagnera vos événements dans l'agenda (à côté des boutons "facebook" et "infos" ). Le relais choisit est mis en valeur par une bordure noire épaisse.</p>

<?php $pagin->affiche() ?>

<table class="table_defaut" >
	<tr>
		<th>Nom</th>
		<th>Style</th>
		<th colspan="4" ></th>
		<th>RSS et Balise javascript</th>
	</tr>
<?php while( $l = $loc->parcours() ) : ?>
	<tr <?php if( $l->code == $str->acc_code_externe() ) : ?>class="actuel"<?php endif ?> >
		<td><?php echo $l->nom ?></td>
		<td><?php echo $l->template ?></td>
		<td>
			<?php if( $l->code == $str->acc_code_externe() ) : ?>
				Actuel
			<?php else : ?>
				<a href="location.php?ids=<?php echo $str->acc_id() ?>&amp;code=<?php echo $l->code ?>" >Choisir</a>
			<?php endif ?>
		</td>
		<td><a href="location-form.php?idl=<?php echo $l->id ?>&amp;ids=<?php echo $str->acc_id() ?>&amp;page=<?php echo $page ?>" >Modifier</a></td>
		<td><a href="location-voir.php?code=<?php echo $l->code ?>&amp;ids=<?php echo $ids ?>" >Voir</a></td>
		<td>
			<a href="<?php echo ADD_SITE.'externe/'.$l->code.'/0_0_FR.rss' ?>" >RSS</a><br />
			<a class="bt_xsl" data-idl="<?php echo $l->code ?>" href="extraction.php?c=<?php echo $l->code ?>" >XSL</a>
		</td>
		<td>rss : <?php echo ADD_SITE.'externe/'.$l->code.'/0_0_FR.rss' ?><br /><br />
		&lt;script type=&quot;text/javascript&quot;&gt;<br />
		&lt;!--<br />
		  IL_Lang = &quot;FR&quot;;<br />
		  IL_Code = &quot;<?php echo $l->code ?>&quot;;<br />
		  IL_Hauteur = &quot;1200&quot;;<br />
		  IL_Largeur = &quot;600&quot;;<br />
		  //--&gt;<br />
		&lt;/script&gt;<br />
		&lt;script src=&quot;http://www.info-limousin.com/externe.php&quot; language=&quot;javascript&quot;&gt;&lt;/script&gt;
		</td>
	</tr>
<?php endwhile ?>
</table>

<div id="option" >
	<form action="extraction.php" method="post" >
	<p>Du : 
	<input name="deb" size="12" type="text" id="deb" value="" style="color:black" />
	<img src="<?php echo RETOUR; ?>jscalendar/img.gif" alt="" id="ddeb" />
	</p>

	<p>Au :
	<input name="fin" size="12" type="text" id="fin" value="" style="color:black" />
	<img src="<?php echo RETOUR; ?>jscalendar/img.gif" alt="" id="dfin" />
	</p>

	<p>Nombre maximal d'événements : <input type="text" size="3" name="max" /></p>
	<p><input id="idl" type="hidden" name="idl" value="" /></p>
	<p><input id="valider" type="submit" name="Ok" value="Valider" /><input id="annuler" type="submit" name="annuler" value="Annuler" /></p>
	</form>
</div>

<script>

Calendar.setup({
	inputField:"deb",     // id of the input field
	ifFormat:"%d/%m/%Y",      // format of the input field
	button:"ddeb",  // trigger for the calendar (button ID)
	singleClick:true
});

Calendar.setup({
	inputField:"fin",     // id of the input field
	ifFormat:"%d/%m/%Y",      // format of the input field
	button:"dfin",  // trigger for the calendar (button ID)
	singleClick:true
});

$(function(){
	var $option = $('#option'); 
	$option.hide(); 
	$option.css({
		'background-color': 'white',
		'position':'absolute',
		'padding':'5px',
		'box-shadow' : '10px 10px 5px #000000',
		'border' : '1px solid black'
	});

	$('.bt_xsl').click(function(){
		var idl = $(this).attr('data-idl'); 
		var poff = $(this).parent().offset(); 

		$('#idl').val(idl); 

		$option.css({ 'top' : poff.top, 'left' : poff.left });
		$option.show(); 
		return false;
	});

	$('#valider').click(function(){
		$option.hide(); 
	});

	$('#annuler').click(function(){
		$option.hide(); 
		return false; 
	});
});

</script>

<?php include BAS_ADMIN ?>
