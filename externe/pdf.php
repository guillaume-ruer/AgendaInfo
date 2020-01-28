<?php
require '../include/init.php'; 
require_once C_INC.'location_fonc.php'; 
require_once C_INC.'fonc_cache.php'; 
require_once C_INC.'departement_class.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'ls_evenement_class.php'; 
require_once C_INC.'evenement_class.php'; 
require_once C_INC.'ville_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'tarif_class.php';
require_once C_INC.'adresse_class.php'; 
require_once C_INC.'fonc_memor.php'; 

define('NB_JOUR_VISIBLE', 120 ); 
define('NB_JOUR_CALENDRIER', 60 );
define('TPS_CACHE', MODE_DEV ? 0 : 0); 

//Récupération du code 


/*
error_reporting(E_ALL); 
ini_set('display_errors', '1'); 
*/


/*
	Récupération des entrée utilisateur 
*/

if(isset($_POST['ok']) )
{
	/*
		Récupération post 
	*/
	$theme = (isset($_POST['theme']) ) ? noui($_POST['theme']) : NULL; 
	$input_date = (isset($_POST['DateDeb']) ) ? date_format_traitement($_POST['DateDeb']) : date('Y-m-d') ; 
	$langue = (isset($_POST['l']) ) ? (int) $_POST['l'] : 1; 
	$code = isset($_POST['c']) ? (int)$_POST['c'] : 0 ;
	$lieu = NULL; 
	if(!empty($_POST['lieu_spe']) )
	{
		$lieu = noui($_POST['lieu_spe']);
	}
	elseif(isset($_POST['lieu']) )
	{
		$lieu = noui($_POST['lieu']);
	}
	
	$groupe_lieu = (isset($_POST['groupe_lieu']) ) ? noui($_POST['groupe_lieu']) : NULL; 
	$id_str = (isset($_POST['ids']) ) ? noui($_POST['ids']) : NULL; 
}
else
{
	/*
		Récupération get 
	*/

	$theme = (isset($_GET['idt']) ) ? noui($_GET['idt']) : NULL ; 

	if( isset($_GET['d']) )
	{
		$input_date = $_GET['d']; 
	}
	elseif( isset($_GET['date']) )
	{
		$input_date = $_GET['date']; 
	}
	else
	{
		$input_date = date('Y-m-d'); 
	}

	$langue = (isset($_GET['l']) ) ? (int) $_GET['l'] : 1; 
	$lieu = (isset($_GET['idl']) ) ? noui($_GET['idl']) : NULL; 
	$groupe_lieu = (isset($_GET['gl']) ) ? noui($_GET['gl']) : NULL; 
	$code = isset($_GET['c']) ? (int)$_GET['c'] : 0; 
	$id_str = (isset($_GET['ids']) ) ? noui($_GET['ids']) : NULL; 
}

//Création des paramètres 
$id_externe = NULL; 

if(!empty($code) )
{
	$donne = req('SELECT * FROM Externe WHERE code='.(int)$code .' LIMIT 1 '); 

	if( $do = fetch($donne)) 
	{
		$id_externe = (int)$do['id']; 
		$code_str = (int)$do['structure']; 
	}
}

//Traitement sur les dates 
$realdate = $datepast = ''; 
mes_date($input_date, NB_JOUR_VISIBLE, $realdate, $datepast);

$dos_fichier = C_DOS_PHP.'cache/'; 
$nom_fichier = 'agenda_info_limousin-'.$code.'-'.$realdate.'-'.$theme.'-'.$lieu.'-'.$groupe_lieu.'-'.$id_str.'.pdf'; 
$fichier = $dos_fichier.$nom_fichier; 

if( !file_exists($fichier) || (filemtime($fichier) + TPS_CACHE < time() ) )
{

/*
	Début mise en cache 
*/

$lsevent = new ls_evenement( array(
	'champ' => EVCH_DATE|EVCH_CAT|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_TARIF|EVCH_LIEU,
	'fi_date_min' => $realdate,
	'fi_date_max' => $datepast,
	'fi_theme' => $theme, 
	'fi_str_actif' => TRUE,
	'fi_id_externe' => $id_externe, 
	'fi_lieu' => $lieu,
	'fi_grp_lieu' => $groupe_lieu, 
	'fi_structure' => $id_str,
	'mode' => reqo::NORMAL
) ); 

$lsevent->requete(); 

/*
	PDF config 
*/

require C_INC.'tcpdf/tcpdf.php'; 

class MYPDF extends TCPDF
{
        public function Header() 
        {   
        }   

        public function Footer() 
        {   
		global $BAS; 
		/*
                // Position at 15 mm from bottom
                $this->SetY(-15);
                // Set font
                $this->SetFont('helvetica', 'I', 8); 
                // Page number
		$date = date('H:i d/m/Y');
                $this->Cell(0, 10, $date." - L'association Info Limousin - http://www.info-limousin.com - Page ".$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		*/

		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->SetY(-30); 
		$html = '<img src="'.C_DOS_PHP.'pdf/'.$BAS.'" width="826px" height="119px" />'; 
		$this->SetFont('dejavusans', '', 8, '', true);
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        } 
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Info Limousin');
$pdf->SetTitle('Agenda');
$pdf->SetSubject('Agenda');
$pdf->SetKeywords('Agenda');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(5, 5, 5);

// set auto page breaks
$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

$presentation ="L'association Info Limousin relaie sur Internet (moteurs 
de recherche, 
agendas, flux RSS thématiques, portails thématiques et géographiques, 
plate-formes sous Androïd...) les informations annonçant des événements 
sur toute la région Limousin. Les adhérents saisissent en ligne leurs 
agendas, l'association les optimise et les diffuse. Elle génère des flux 
d'informations en usage libre utilisés sur de nombreux sites Internet 
et outils de flux.";
$contact = "contact@info-limousin.com / www.asso.info-limousin.com / 
www.info-limousin.com";

if( !empty($id_str) )
{
	$donne = req('SELECT pdf_haut, pdf_bas FROM structure WHERE id='.(int)$id_str.' LIMIT 1 ');
	if( $do = fetch($donne) )
	{
		$haut = $do['pdf_haut']; 
		$BAS = $do['pdf_bas']; 
	}

}

if( (empty($haut) || empty($BAS) ) &&  $id_str != $code_str)
{
	$donne = req('SELECT pdf_haut, pdf_bas FROM structure WHERE id='.(int)$code_str.' LIMIT 1 ');

	if( $do = fetch($donne) )
	{
		$haut = $do['pdf_haut']; 
		$BAS = $do['pdf_bas']; 
	}
}

if( empty($haut) )
{
	$haut = rappel('pdf_haut');
}

if( empty($BAS) )
{
	$BAS = rappel('pdf_bas');
}

$html = '<img src="'.C_DOS_PHP.'pdf/'.$haut.'" />';

$pdf->SetFont('dejavusans', '', 8, '', true);

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->SetFont('dejavusans', '', 10, '', true);

$pdf->setY( $pdf->getY() + 10); 

$nom_theme = (!empty($theme ) ) ? nom_theme($theme) : ' tous les thèmes ';
$datedeb = date_format_fr($realdate); 

if(!empty($lieu) )
{
	$do_lieu = nom_lieu($lieu); 
	$zone = $do_lieu['nom_lieu'];
}
elseif(!empty($groupe_lieu ) )
{
	$zone = nom_groupe_lieu($groupe_lieu ); 
}
elseif( isset($do['titre_flux']) )
{
	$zone = secuhtml($do['nom']); 
}
else
{
	$zone = 'TOUT LE LIMOUSIN'; 
}

$titre = '<strong>'.mb_strtoupper('FLUX RSS à partir du ' . $datedeb . ' - '.$nom_theme, 'UTF-8' ).'</strong>'; 
$pdf->writeHTMLCell(0, 0, '', '', $titre, 0, 1, 0, true, '', true);

$pdf->setY( $pdf->getY() + 10); 


while($e = $lsevent->parcours() )
{
	if( $pdf->getY() > 230 )
	{
		$pdf->AddPage(); 
	}

	ob_start(); 
	$e->acc_categorie()->aff(); 
	$symbole = ob_get_contents();
	ob_end_clean(); 

	$html = '<div>'; 
	$html .= '<table><tr>'; 
	$html .= '<td style="width:40px" >'.$symbole.'
	</td>
	<td style="width:20px" ></td>	
	'; 

	ob_start();
	$e->aff_date();
	$date = ob_get_contents(); 
	ob_end_clean(); 

	$html .= '<td style="width:600px" ><strong>'.$date.' - '.secuhtml($e->acc_titre() ).' - ';
	$virg=''; 

	foreach($e->acc_tab_lieu() as $v )
	{
		$html .= $virg;
		$html .= secuhtml( $v->acc_nom() );
		$html .= '&nbsp;('.secuhtml( $v->acc_dep()->acc_num() ) .')'; 
		$virg = ', ';
	}
	
	$html .= '</strong><br />'; 
	$html .= ''.secuhtml($e->acc_desc() ).'<br />'; 
	$html .='<em>';
	$html .= secuhtml( $e->acc_contact()->acc_structure()->acc_nom() );
	$html .= ' '.secuhtml( $e->acc_contact()->acc_titre() ); 
	


	if( $e->acc_source() == evenement::LEI )
	{
		$html .= ' (source LEI)'; 
	}

	$html .=' '.secuhtml( $e->acc_contact()->acc_tel() ); 

	if( $e->acc_contact()->acc_site() != '' )
	{
		$html .= ' '.secuhtml($e->acc_contact()->acc_site() ); 
	}

	$html .='</em></td>'; 
	$html .= '</tr></table></div>'; 
	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
}

$pdf->Output($fichier, 'F');

// Fin mise en cache 
}

header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.$nom_fichier.'"');
readfile($fichier); 
