<?php
require '../include/init.php'; 


require C_INC.'ls_evenement_class.php'; 
require C_INC.'fonc_cache.php'; 

define('TPS_CACHE', MODE_DEV ? 0 : 1800); 
define('JSON', 0);
define('XML', 1); 
define('NB_JOUR_VISIBLE', 120); 

function url_image($e)
{
	$ui = ''; 
	if( $e->acc_image() ) 
	{
		$ui = secuhtml( 'http://info-limousin.com/dos-php/event_image/'.$e->acc_image() );
	}
	elseif( $e->acc_contact()->acc_structure()->acc_logo() ) 
	{
		$ui= secuhtml('http://info-limousin.com/img/logos/'.$e->acc_contact()->acc_structure()->acc_logo() ); 
	}
	return $ui; 
}

http_param( array('type'=> JSON) ); 

$input_date = (isset($_GET['date']) ) ? $_GET['date'] : date('Y-m-d');
$dateu = (isset($_GET['dateu']) ) ? $_GET['dateu'] : NULL; 
$lieu = (isset($_GET['idl']) ) ? noui($_GET['idl']) : NULL;

$theme = NULL; 
if( !empty($_GET['idt']) )
{
	$theme = array_map('intval', explode(',', $_GET['idt']) ); 
}

$page = (isset($_GET['pg']) ) ? (int)$_GET['pg'] : 0 ;
$groupe_lieu = (isset($_GET['gl']) ) ? noui($_GET['gl']) : NULL;
$nb_par_page = (isset($_GET['npp']) ) ? (int)$_GET['npp'] : 20;
$code = (isset($_GET['code']) ) ? absint($_GET['code']) : NULL; 
$id_str = (isset($_GET['ids']) ) ? noui($_GET['ids']) : NULL; 
$opt = (isset($_GET['opt']) ) ? (bool)$_GET['opt'] : FALSE; 

if( $nb_par_page > 100 )
{
	$nb_par_page = 100; 
}
elseif( $nb_par_page < 0 )
{
	$nb_par_page = NULL; 
}

$id_cache= cache_id($input_date, $dateu, $nb_par_page, $lieu, implode('_', $theme), $page, $groupe_lieu, $code, $id_str, $opt, $type == JSON ? 'json' : 'xml' );

if( $type == JSON )
{
	header('content-type: application/json ');
}
else
{
	header('content-type: text/xml charset="utf8" ');
}

if(cache($id_cache, TPS_CACHE  ) )
{

$tab_str=array();
$ide = NULL; 

if( !is_null($code) )
{
	$donne = req('SELECT id FROM Externe WHERE code='.(int)$code.' LIMIT 1 '); 
	
	if( $do = fetch($donne) )
	{
		$ide = (int)$do['id']; 
	}
}

$tab_str=array();
$tab_theme = array(); 

if( $opt )
{
	$lse = new ls_evenement(array(
		'champ' => EVCH_CAT|EVCH_CONTACT|EVCH_CAT_GROUPE,
		'fi_id_externe' => $ide, 
		'fi_str_actif' => TRUE, 
		'mode' => reqo::NORMAL, 
	)); 

	$lse->requete(); 
	while( $e = $lse->parcours() )
	{
		$tab_str[ $e->acc_contact()->acc_structure()->acc_id() ] = array(
			'id' => $e->acc_contact()->acc_structure()->acc_id(),
			'nom' => $e->acc_contact()->acc_structure()->acc_nom(),
		);

		$tab_theme[ $e->acc_categorie()->acc_id() ] = array( 
			'id' => $e->acc_categorie()->acc_groupe(),
			'val' => $e->acc_categorie()->acc_groupe_nom(),
		);
	}

	usort($tab_str, function($a, $b){
		return strcmp($a['nom'], $b['nom']); 
	}); 

	usort($tab_theme, function($a, $b){
		return strcmp($a['val'], $b['val']); 
	}); 
}

$realdate = $datepast = '';

if( !is_null($dateu) )
{
	mes_date($dateu, 0, $realdate, $datepast);
}
else
{
	mes_date($input_date, NB_JOUR_VISIBLE, $realdate, $datepast);
}

$lse = new ls_evenement(array(
	'champ' => EVCH_DATE|EVCH_CAT|EVCH_LIEU|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_CAT_GROUPE,
	'fi_date_min' => $realdate,
	'fi_date_max' => $datepast,
	'fi_lieu' => $lieu,
	'fi_grp_lieu' => $groupe_lieu,
	'fi_theme' => $theme,
	'fi_id_externe' => $ide, 
	'fi_str_actif' => TRUE, 
	'fi_structure' => $id_str, 
	'mode' => is_null($nb_par_page) ? reqo::NORMAL : reqo::PAGIN, 
	'avoir_nb_entre' => TRUE,
)); 

$lse->acc_pagin()->mut_num_page($page); 
$lse->mut_nb_par_page($nb_par_page); 
$lse->requete(); 

switch($type)
{
	case XML :
	?>
<?php echo '<?php echo "<?xml version=\"1.0\" ?>" ?>' ?>
	<liste-evenement>
	<meta>
		<donne name="nb_evenement" value="<?php echo $lse->acc_nb_entre() ?>" />
		<donne name="date_debut" value="<?php echo $realdate ?>" />
		<donne name="date_fin" value="<?php echo $datepast ?>" />
		<donne name="date_jour" value="<?php echo date('Y-m-d') ?>" />
	</meta>
	<liste num_page="<?php echo $page ?>" nb_par_page="<?php echo $nb_par_page ?>" >
<?php while( $e = $lse->parcours() ) : ?>
	<evenement id="<?php $e->aff_id() ?>" >
		<titre><?php $e->aff_titre() ?></titre>
		<description><?php $e->aff_desc(FALSE) ?></description>
		<date format="yyyy-mm-dd" ><?php echo $e->acc_tab_date(0) ?></date>
		<date format="text" lng="fr" ><?php $e->aff_date() ?></date>
		<nb-date><?php echo $e->acc_nb_date() ?></nb-date>
		<image><?php echo url_image($e) ?></image>
		<contact id="<?php $e->acc_contact()->aff_id() ?>" >
			<titre><?php $e->acc_contact()->aff_titre() ?></titre>	
			<lien><?php $e->acc_contact()->aff_site() ?></lien>	
			<tel><?php $e->acc_contact()->aff_tel() ?></tel>	
			<structure id="<?php $e->acc_contact()->acc_structure()->aff_id() ?>" >
				<nom><?php $e->acc_contact()->acc_structure()->aff_nom() ?></nom>
				<code-externe><?php echo $e->acc_contact()->acc_structure()->acc_code_externe() ?></code-externe>
			</structure>
		</contact>
		<categorie id="<?php echo $e->acc_categorie()->acc_id() ?>" >
			<nom><?php echo $e->acc_categorie()->acc_nom() ?></nom>
			<img><?php echo $e->acc_categorie()->acc_img() ?></img>
			<groupe id="<?php echo $e->acc_categorie()->acc_groupe() ?>" >
				<?php echo $e->acc_categorie()->acc_groupe_nom() ?>
			</groupe>
		</categorie>
		<liste-ville>
		<?php foreach($e->acc_tab_lieu() as $lieu) : ?>
			<ville id="<?php $lieu->aff_id() ?>" >
			<nom><?php $lieu->aff_nom() ?></nom>
			</ville>
		<?php endforeach ?>
		</liste-ville>
	</evenement>
<?php endwhile ?>
	</liste>
	</liste-evenement>
<?php
	break;
	default :
		$tab = array(); 

		while( $e = $lse->parcours() ) 
		{
			$tab[] = $e->tab(); 
		}

		echo json_encode(array(
			'meta' => array(
				'nb_evenement' => $lse->acc_nb_entre(), 
				'nb_page' => $lse->acc_nb_page(), 
				'date_deb' => $realdate, 
				'date_fin' => $datepast,
				'date_jour' => date('Y-m-d'),
				'structure' => $tab_str, 
				'theme' => $tab_theme
			),
			'liste_evenement' => $tab
		)); 
	break;
}

// Fin cache 
}
cache(); 
