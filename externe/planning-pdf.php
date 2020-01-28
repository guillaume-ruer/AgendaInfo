<?php
require '../include/init.php'; 
require_once C_INC.'location_fonc.php'; 
require_once C_INC.'fonc_cache.php'; 
require_once C_INC.'fonc_memor.php'; 
require_once C_INC.'planning_fonc.php'; 

/*
	Début mise en cache 
*/

//Récupération du code 
$param = json_decode($_POST['param'], true); 

$lsevent = new ls_evenement( array(
	'champ' => EVCH_DATE|EVCH_CAT|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_TARIF|EVCH_LIEU,
	'fi_event_id' => array_keys($param), 
	'mode' => reqo::NORMAL
) ); 

$lsevent->requete(); 
$tabf = []; 

while($ev = $lsevent->parcours() ) 
{
	$tabf[ (int)$param[ $ev->acc_id() ]['num'] ] = $ev; 
}

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
		$this->SetY(-20); 
		$this->Cell(0,0,'', ['T'=>['color'=>[200,200,200], 'width' => 0.2 ]], 1 );
		$this->SetFont('dejavusans', '', 10, '', true);
		$this->Cell(0, 0, "Association Info Limousin - 15, bld Victor Hugo 87120 Eymoutiers", '', 1, 'C');
		$this->Cell(0, 0, "contact@info-limousin.com - www.asso.info-limousin.com - 09 77 84 02 55", '', 1, 'C');
		$this->Cell(0, 0, "n°siret : 491 139 143 00029 - n°NAF : 9499Z", '', 1, 'C');
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

define('IMG_HEIGHT', 7); 
define('IMG_WIDTH', 7); 

$pdf->AddPage();

$pdf->Image( '../img/planning/logo_info_limousin_300px.jpg', 4, 4);
$suivy = $pdf->getY()+38; 

$deb_texte = $pdf->getX() + 90; 
$texte_width = 110;
$pdf->setY($pdf->getY() + 3); 
$pdf->setFont('','', 10); 
$pdf->setX($deb_texte); 
$pdf->MultiCell($texte_width, 0, "L’association Info Limousin relaie sur Internet les informations annonçant des événements sur le Limousin."
	." Les adhérents saisissent en ligne leurs agendas, l’association les optimise et les diffuse. Elle génère de nombreux "
	." flux d'informations en usage libre utilisés sur de nombreux sites Internet et outils de flux. ", '', 'J', '', 2); 
$pdf->setX($deb_texte); 
$pdf->setFont('','BU'); 
$pdf->Cell($texte_width, 0, "www.info-limousin.com", '', 1, 'R');
$pdf->setFont('',''); 

$pdf->setY($suivy); 

$tabm = tab_mois();

$tab_jr = [0,0,0,0]; 

do
{
	$continue = FALSE; 	
	$px = $pdf->getX(); 
	$py = $pdf->getY(); 

	$i=0; 
	foreach($tabm as $idm => list($mnum, $ma, $mnom, $tjour) )
	{
		$cx = $px + $i*52; 
		$i++; 

		$pdf->setXY($cx, $py); 

		if( $tab_jr[$idm] < count($tjour) )
		{
			$pdf->setFont('','B'); 
			$pdf->Cell(
				50, // Width
				0, // Height
				$mnom.' '.$ma, // Txt 
				'', // Border
				2, // Ou placer le pointeur après 0:droite, 1:début de la prochaine ligne, 2:en dessous
				'C' // Alignement C:center, R:right, J:justify, L:left (defaut)
			); 
			$pdf->setFont('', ''); 
		}

		$cy = $pdf->getY();  

		for( $indj=$tab_jr[$idm]; $indj<count($tjour); $indj++ )
		{
			list($num,,$we,$l,$date,, $ns)=$tjour[$indj];
			$pdf->setXY($cx,$cy); 
			$sepx = $cx; 
			$sepy1 = $cy; 

			$tabi=[]; 
			foreach($param as $id => $ev )
			{
				if( isset($ev['date'][$date]) && $ev['date'][$date] )
				{
					$tabi[] = $ev; 
				}
			}

			$nbi = count($tabi); 

			if( $nbi > 0 )
			{
				$nb_par_ligne = floor(38/IMG_WIDTH); 
				$nb_ligne = ceil($nbi/$nb_par_ligne); 
			}
			else
			{
				$nb_par_ligne = 0; 
				$nb_ligne = 1; 
			}

			$hauteur = $nb_ligne * IMG_HEIGHT; 

			if( $pdf->getY() + $hauteur > 270 )
			{
				$continue = TRUE; 
				$tab_jr[$idm] = $indj; 
				break; 
			}
			else
			{
				$tab_jr[$idm]++; 
			}

			// Fond clair pour les weekend
			if($we) 
			{
				$pdf->setFillColor(220); 
				$pdf->Cell( 50, $hauteur, '', null, 0, '', true); 
			}

			// Barre de séparation des jours 
			$pdf->setXY($sepx,$sepy1);
			$pdf->Cell(50,$hauteur,'',['B'=>['color'=>[200,200,200], 'width'=>(($ns==7) ? 1 : 0.2) ]],2); 


			// Ecriture numéro du jour et première lettre
			$pdf->setXY($cx, $cy); 

			$pdf->Cell(
				7, // Width
				$hauteur, // Height
				$num, // Txt 
				'', // Border
				0, // Ou placer le pointeur après 0:droite, 1:début de la prochaine ligne, 2:en dessous
				'C' // Alignement C:center, R:right, J:justify, L:left (defaut)
			);

			$pdf->Cell(
				5, // Width
				$hauteur, // Height
				$l, // Txt 
				'', // Border
				2, // Ou placer le pointeur après 0:droite, 1:début de la prochaine ligne, 2:en dessous
				'C' // Alignement C:center, R:right, J:justify, L:left (defaut)
			);

			// Ecriture des jeton des événements
			if( $nbi > 0 )
			{
				$iy = $cy; 
				$diy = $iy; 
				$cy = $cy+$hauteur; 
				$nbp=0; 
				$ix = $pdf->getX()+5; 

				for($k=0; $nbp<$nbi && $k<$nb_ligne; $k++)
				{
					$iy = $diy+IMG_HEIGHT*$k; 
					$pdf->setY($iy); 
					$pdf->setX($ix); 

					for($j=0;$nbp<$nbi && $j<$nb_par_ligne; $j++ )
					{
						$tmpx = $pdf->getX(); 
						$tmpy = $pdf->getY(); 

						$symfile = '../img/symboles/'.$tabf[ $tabi[$nbp]['num'] ]->acc_categorie()->acc_img();
						$infosym = pathinfo($symfile);
						
						if($infosym['extension'] == 'svg' )
						{
							$pdf->ImageSVG( $symfile, '', '', IMG_WIDTH, IMG_HEIGHT );
						}
						else
						{
							$pdf->Image( $symfile, '', '', IMG_WIDTH, IMG_HEIGHT );
						}

						$pdf->setXY($tmpx, $tmpy); 
			
						$pdf->Cell(
							IMG_WIDTH, // Width
							IMG_HEIGHT, // Height
							$tabi[$nbp]['num'], // Txt 
							'', // Border
							0, // Ou placer le pointeur après 0:droite, 1:début de la prochaine ligne, 2:en dessous
							'C' // Alignement C:center, R:right, J:justify, L:left (defaut)
						);

						$nbp++; 
					}
				}
			}
			else
			{
				$cy = $pdf->getY(); 
			}
		}
	}
		
	if( $continue ) 
	{
		$pdf->AddPage(); 
	}
}
while($continue); 

$pdf->AddPage(); 

for($i=1, $first=TRUE; $i<=count($tabf); $i++ )
{
	$e = $tabf[$i]; 

	$pdf->startTransaction(); 
	if( !$first ) $pdf->Cell(0,0,'', ['T'=>['color'=>[200,200,200], 'width' => 0.2 ]], 1 );
	$first = FALSE; 

	$evx = $pdf->getX(); 
	$evy = $pdf->getY(); 

	$sym = $e->acc_categorie()->acc_img(); 
	$infosym = pathinfo($sym); 

	if( $infosym['extension'] == 'svg') 
	{
		$pdf->ImageSVG(C_IMG.'symboles/'.$sym,$evx,$evy,20,20);    
	}
	else
	{
		$pdf->Image(C_IMG.'symboles/'.$sym,$evx,$evy,20,20);    
	}
	
	$num = $param[$e->acc_id() ]['num']; 
	$pdf->setY($pdf->getY()+20); 

	$pdf->Circle(
		$pdf->getX()+10,
		$pdf->getY()+10,
		10,
		0,360,'F', [], [140,140,140]
	);

	$af = $pdf->getFontSizePt(); 
	$pdf->setFont('', ''); 
	$pdf->setFontSize($af+10); 
	$pdf->setColor('text',255,255,255); 
	$pdf->Cell(
		20, // Width
		20, // Height
		$num, // Txt 
		'', // Border
		2, // Ou placer le pointeur après 0:droite, 1:début de la prochaine ligne, 2:en dessous
		'C' // Alignement C:center, R:right, J:justify, L:left (defaut)
	);
	$pdf->setColor('text', 0,0,0);
	$pdf->setFontSize($af); 

	$evcgy = $pdf->getY(); 
	$pdf->setXY($evx+22,$evy); 

	ob_start();
	$e->aff_date();
	$date = ob_get_contents(); 
	ob_end_clean(); 

	$pdf->setFont('', 'B'); 
	$pdf->MultiCell(0, 0, ucfirst($date).' - '.$e->acc_titre(), '', '', '', 2);

	$virg=''; 
	$lieu = ''; 

	foreach($e->acc_tab_lieu() as $v )
	{
		$lieu .= $virg;
		$lieu .= $v->acc_nom();
		$lieu .= ' ('.$v->acc_dep()->acc_num() .')'; 
		$virg = ', ';
	}
	
	$pdf->setXY($evx+22, $pdf->getY() ); 
	$pdf->MultiCell(0, 0, $lieu, '', '', '', 2);
	$pdf->setFont('', ''); 

	$pdf->setXY($evx+22, $pdf->getY() ); 
	$pdf->MultiCell(170, 0, $e->acc_desc(), '', '', '', 2 ); 

	$pdf->setFont('', 'i'); 
	$contact = $e->acc_contact()->acc_structure()->acc_nom();
	$contact .= ' '.$e->acc_contact()->acc_titre(); 
	
	if( $e->acc_source() == evenement::LEI )
	{
		$contact .= ' (source LEI)'; 
	}

	$contact .=' '.$e->acc_contact()->acc_tel(); 

	if( $e->acc_contact()->acc_site() != '' )
	{
		$contact.= ' '.$e->acc_contact()->acc_site(); 
	}

	$pdf->setXY($evx+22, $pdf->getY()+3 ); 
	$pdf->MultiCell(0,0,$contact, '','', '', 1); 

	$pdf->setXY($evx, max($evcgy, $pdf->getY() ) ); 

	if( $pdf->getY() > 260 )
	{
		$pdf->rollbackTransaction(TRUE); 
		$pdf->addPage(); 
		$first=TRUE; 
		$i--; 
	}
	else
	{
		$pdf->commitTransaction(); 
		$pdf->setY($pdf->getY()+5); 
	}
}

planning_add_stat($tabf); 

$pdf->Output('mon_planning_info-limousin.pdf', 'D');
