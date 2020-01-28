<?php
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'structure_facture_fonc.php'; 
require_once C_INC.'facture_pdf_fonc.php'; 

function structure_payment($do)
{
	$ps = $do['ps']; // Vérifier que c'est terminé : Completed 
	$f = $do['f'];  // Numéro de la facture
	$somme = $do['somme']; // Somme encaissé
	$txn_id = trim($do['txn_id']); // Vérifier les doublons
	$id_paypal = $do['id_paypal']; // Retrouver la str

	if( $ps != 'Completed')
	{
		return; 
	}

	// Retrouver la structure concerné par ce paiement via un numéro unique
	$req = prereq('SELECT id FROM structure WHERE id_paypal=?');
	exereq($req, [$id_paypal]); 
	$do = fetch($req); 
	$str = str_init($do['id']); 

	$valide = TRUE; 

	// Vérifier que txn_id n'est pas déjà présent 

	if(!empty($txn_id) )
	{
		$donne = exepre('SELECT * FROM structure_facture WHERE paypal_txn_id LIKE(?)', [$txn_id]); 

		if( $do = fetch($donne) )
		{
			$valide = FALSE; 
		}
	}

	// Comparer le prix de la facture et la somme reçu. 
	$cout = $str->cout_annuel(); 

	if( $cout != $somme )
	{
		$valide = FALSE; 
	}

	if( $valide )
	{
		// Mettre à jour la date de fin d'adhésion 
		$ad = $str->acc_date_fin_adhesion(); 
		$ajd = time(); 

		if( $str->acc_actif() == structure::ATTENTE )
		{
			$dep = ($ad > $ajd) ? $ad : $ajd; 
			list($j,$m,$a) = explode('/', date('d/m/Y', $dep) ); 
			$ar = mktime(0,0,0,$m,$j,$a+1); 
		}
		elseif( $ajd < $ad+30*3*24*3600 ) 
		{
			/* 
				Paiement avant la fin du troisème mois de relance :
					on prend la date anniversaire.
			*/

			list($j,$m,$a) = explode('/', date('d/m/Y', $ad) ); 
			$ar = mktime(0,0,0,$m,$j,$a+1); 
		}
		else
		{
			$dep = $ajd; 
			list($j,$m,$a) = explode('/', date('d/m/Y', $dep) ); 
			$ar = mktime(0,0,0,$m,$j,$a+1); 
		}

		// Changer l'était si la str était en attente. 
		$ch = ''; 
		if( $str->acc_actif() == structure::ATTENTE )
		{
			$ch = ', actif='.structure::ACTIF.' '; 
		}

		req('UPDATE structure SET date_fin_adhesion='.(int)$ar.', rappel=0, rappel_facture=\'\''.$ch.' WHERE id='.(int)$str->acc_id().' LIMIT 1'); 

		// Générer la facture
		$dos = structure_facture_dossier(); 
		$dof = genere_facture_pdf($str->acc_id(), $dos); 

		exepre('INSERT INTO structure_facture(structure, date, somme,type,dossier,fichier, paypal_txn_id)
			VALUES(?,?,?,?,?, ?,?)',	
			[
				(int)$str->acc_id(),
				time(),
				(float)$somme,
				structure_facture::PAYPAL,
				str_replace('../', '', $dof['dos']), 
				secubdd($dof['fichier']),
				$txn_id	
			]
		); 
	}
}
