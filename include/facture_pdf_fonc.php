<?php
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'tcpdf/tcpdf.php'; 
require_once C_INC.'fonc_memor.php'; 

class MYPDF extends TCPDF
{
	public function Header() 
	{   

	}   

	public function Footer() 
	{   
		/*
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8); 
		// Page number
		$date = date('H:i d/m/Y');
		$this->Cell(0, 10, $date." - L'association Info Limousin - http://www.info-limousin.com - Page ".$this->getAliasNumPage().'/'.$this->getAliasNbPages()
			, 0, false, 'C', 0, '', 0, false, 'T', 'M'
		);
		*/

		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->SetY(-30); 
		$html = '<img src="'.RETOUR.'img/facture/bandeau_bas_pdf_info_limousin.jpg" width="826px" height="119px" />'; 
		$this->SetFont('dejavusans', '', 8, '', true);
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	} 
}

function genere_facture_config()
{
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Info Limousin');
	$pdf->SetTitle('Facture');
	$pdf->SetSubject('Facture');
	$pdf->SetKeywords('Facture');
	$pdf->SetFont('dejavusans', '', 11, '', true);

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

	return $pdf; 
}

function genere_facture_pdf($ids, $dos)
{
	$str = str_init($ids); 

	$pdf = genere_facture_config(); 
	$var_memor = 'facture-numero';
	$num = rappel($var_memor, 1); 
	memor($var_memor, $num+1); 
	$numero = sprintf("%'.04d", $num ); 
	$mois = date('m'); 
	$annee = date('y'); 

	$nom = 'FEL'.$numero.'-'.$mois.'-'.$annee.'-'.$str->nom_normalise(); 
	
	$fichier = $nom.'.pdf'; 
	$chenr = $dos.'/'.$fichier; 

	$html = '<img src="'.RETOUR.'img/facture/bandeau_haut_pdf_info_limousin.jpg" width="826px" />'; 
	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

	if( $str->acc_actif() == structure::ATTENTE )
	{
		$terme = 'Devis'; 
	}
	else
	{
		$terme = 'Facture'; 
	}

	$html = '<p>'.$terme.' '.$nom." </p>"; 
	
	$tab_cout = []; 

	if( $str->payant() )
	{
		$tab_cout[] = "Adhésion - 365 jours : ".structure::cout($str->acc_type() )."€"; 
	}

	$opt = $str->abo_option(); 

	foreach($opt as $o )
	{
			$tab_cout[] = $o->acc_description().' : '.$o->acc_prix().'€'; 
	}

	$html .= '<p>'; 
	$html .= $str->acc_nom().'<br />'; 
	$html .= $str->acc_adresse()->acc_rue().'<br />'; 

	$cp = $str->acc_adresse()->acc_ville()->acc_cp();
	if( !empty($cp) )
	{
		$html .= $cp.' '; 
	}

	$html .= $str->acc_adresse()->acc_ville()->acc_nom(); 

	$html .= '</p>'; 

	$html .= '<p>';

	$html .= implode('<br />', $tab_cout)."<br />"; 
//	$html .= "Date anniversaire : ".moi_num2str( (int)strftime("%m", $str->acc_date() ) )."<br />";

	$date = jr_num2str(date('w') ).' '.date('d').' '.moi_num2str( (int)date('m') ).' '.date('Y'); 

	$html .= "<br />Date : ".$date."<br />"; 
	$html .= "Adhérent n°".$str->acc_numero(); 
	$html .= "</p>";

	$html .= "<p>Site Internet pour la diffusion : http://www.agenda-dynamique.com <br />"; 

	$donne = req('SELECT User FROM Utilisateurs WHERE id_structure='.$str->acc_id().' ');

	if( $do = fetch($donne) )
	{
		$html .= 'Cliquez sur "accès à la plate-forme de diffusion"'; 
		$html .= '<br />Saisissez votre nom d\'utilisateur : '.$do['User'];
	}
	$html .= '</p>'; 

	if( empty($opt) )
	{

	}
	else
	{
		$html .= "<p>L'association Info Limousin achetent des abonnements annuels de composants pour Joomla, certains sont compris dans l'adhésion, d'autres demandent une contribution financière.<br /><br />
Document plateforme : http://urlz.fr/7LW4 <br />
Accompagnement Joomla (document) : http://urlz.fr/7pGd <br />
Accompagnement Joomla (vidéo) : https://www.asso.info-limousin.com/joomla/accompagnement <br />
Questions-Réponses Joomla : https://www.asso.info-limousin.com/joomla/questions-reponses
		</p>
		";
	}


	$html .="<p>Règlement par chèque à l'association Info Limousin<br />
	Virement bancaire : La Banque Populaire Aquitaine Centre Atlantique<br />
	IBAN : FR76 1090 7002 7296 0218 3157 130 / BIC : CCBPFRPPBDX<br />
	Association non assujettie à la TVA
	</p>";

	$html .='<p><strong>Montant forfaitaire annuel : '.$str->cout_annuel().'€</strong></p>'; 

	$html = lien_text($html); 

	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

	$pdf->Output($chenr, 'F');

	return [
		'fichier' => $fichier,
		'dos' => $dos,
		'chenr' => $chenr,
	]; 
}
