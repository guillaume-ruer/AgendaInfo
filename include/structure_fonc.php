<?php
require_once C_INC.'structure_class.php'; 
require_once C_INC.'location_class.php'; 

function str_location_choix($str, $code )
{
	req('UPDATE structure SET code_externe='.(int)$code.' WHERE id='.(int)$str->acc_id().' LIMIT 1');
	$str->mut_code_externe($code); 
}

function str_etat($id, $etat)
{
	req('UPDATE structure SET actif='.absint($etat).' WHERE id='.absint($id).' LIMIT 1 ');			
}

function str_sup($id)
{
	static $s1=NULL, $s2, $s3; 

	if(is_null($s1) )
	{
		$s1 = prereq('DELETE FROM structure WHERE id=? LIMIT 1 ');
		$s2 = prereq('DELETE FROM structure_droit WHERE structure=? LIMIT 1 '); 
		$s3 = prereq('DELETE FROM structure_contact WHERE id_structure=? LIMIT 1 '); 
	}

	exereq($s1, array($id) ); 
	exereq($s2, array($id) ); 
	exereq($s3, array($id) ); 
}

function str_mod_droit($ids, $idu, $droit)
{
	static $s=NULL, $i=NULL, $u=NULL, $d=NULL;

	if(is_null($s) )
	{
		$s=prereq('SELECT utilisateur FROM structure_droit WHERE utilisateur=? AND structure=? LIMIT 1 '); 
		$i=prereq('INSERT INTO structure_droit(droit, utilisateur, structure) VALUES(?,?,?)'); 
		$u=prereq('UPDATE structure_droit SET droit=? WHERE utilisateur=? AND structure=? LIMIT 1 '); 
		$d=prereq('DELETE FROM structure_droit WHERE utilisateur=? AND structure=? LIMIT 1 '); 
	}

	if(empty($droit) )
	{
		exereq($d, array( $idu, $ids) ); 
	}
	else
	{
		exereq($s, array( $idu, $ids ) ); 

		if( fetch($s) )
		{
			exereq($u, array($droit, $idu, $ids ) ); 
		}
		else
		{
			exereq($i, array($droit, $idu, $ids ) ); 
		}
	}
}

function str_upload($visuel, $name )
{
	$return = FALSE; 
	switch($visuel)
	{
		case structure::LOGO :
			$return = tcimg($name, C_IMG.'logos/', 'jpeg,jpg', 160, 90, TRUE);  
		break;
		case structure::BANNIERE : 
			$return = tcimg($name, C_IMG.'bandeaux/', 'gif', 120, 60, FALSE );
		break; 
	}
	return $return; 
}

function str_droit_utilisateur( $ids, $droit=STR_MODIFIER )
{
	static $dr =NULL;
	global $MEMBRE; 

	if( is_null($dr ) )
	{
		$dr = prereq('SELECT droit FROM structure_droit WHERE utilisateur=? AND structure=? '); 
	}

	if( ($MEMBRE->id_structure == $ids) || droit(GERER_UTILISATEUR) )
	{
		return TRUE; 
	}
	else 
	{
		exereq($dr, array($MEMBRE->id, $ids ) ); 
		$do = fetch($dr); 
		return  (bool)($do['droit'] & $droit);   
	}
}

function str_enr($s)
{
	return $s->acc_id()==0 ? str_ins($s) : str_maj($s) ; 
}

function str_maj($s)
{
	$champ = 'nom=?, ville=?, adresse=?, 
		email=?, mail_rq=?, facebook=?, presentation=?,
		banniere=?, banniere_url=?, conv=?, payant=?'; 

	$tab = array( $s->acc_nom(), $s->acc_adresse()->acc_ville()->acc_id(), 
		$s->acc_adresse()->acc_rue(), $s->acc_mail(), $s->acc_mail_rq(),
		$s->acc_facebook(), $s->acc_desc(),  
		$s->acc_banniere(), $s->acc_banniere_url(), $s->acc_conv(), $s->acc_payant()
	); 

	if( $s->modif(structure::M_LOGO) )
	{
		$tab[] = $s->acc_logo(); 
		$champ .= ', logo=?'; 
	}
		
	if( $s->modif(structure::M_TYPE) )
	{
		$tab[] = $s->acc_type(); 
		$champ .=', type=?'; 
	}

	if( $s->modif(structure::M_ACTIF) )
	{
		$tab[] = $s->acc_actif(); 
		$champ .= ', actif=?'; 
	}
	
	if( $s->modif(structure::M_NUM) )
	{
		$tab[] = $s->acc_numero(); 
		$champ .=', numero=?'; 
	}

	$maj = prereq('UPDATE structure SET '.$champ.' WHERE id=? LIMIT 1 '); 

	$tab[] = $s->acc_id();

	exereq($maj, $tab); 
	str_enr_contact($s); 

	mess("Mise à jour effectuée."); 
	return $s; 
}

function str_enr_contact($s)
{
	$tab_idc = array(); 

	foreach($s->acc_tab_contact() as $c )
	{
		$c->mut_structure($s); 
		contact_enr($c); 
		$tab_idc[] = $c->acc_id(); 
	}

	$tab_idc = array_unique($tab_idc); 
	contact_sup_not_str($tab_idc, $s->acc_id() ); 
}

function str_ins($s)
{
	static $r = NULL; 
	if(is_null($r) )
	{
		$r = prereq('INSERT INTO structure(nom, numero, actif, ville, adresse, email, mail_rq, facebook, 
			presentation, logo, date_adhesion, type, banniere, banniere_url, conv, payant )
			VALUES(?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ? )
		'); 	
	}

	exereq($r, array($s->acc_nom(), $s->acc_numero(), $s->acc_actif(), $s->acc_adresse()->acc_ville()->acc_id(), 
		$s->acc_adresse()->acc_rue(), $s->acc_mail(), $s->acc_mail_rq(), 
		$s->acc_facebook(), $s->acc_desc(), $s->acc_logo(), time(), $s->acc_type(), 
		$s->acc_banniere(), $s->acc_banniere_url(), $s->acc_conv(), $s->acc_payant() 
	) ); 

	$s->mut_id(derid() ); 
	$s = str_enr_contact($s); 
	mess('Nouvelle structure enregistrée.');
	return $s; 
}

function str_init($id)
{
	$ids = absint($id); 
	$donne = req('
		SELECT s.id, s.numero, s.nom, s.logo, s.adresse, l.Lieu_Ville AS ville, s.ville id_ville, 
			l.Lieu_CP cp, s.presentation description, s.email, l.Lieu_Dep dep, 
			s.type, s.date_adhesion date, 
			s.actif, banniere, banniere_url, mail_rq, code_externe, conv, payant, id_paypal,
			date_fin_adhesion, rappel, rappel_facture
		FROM structure AS s
		LEFT JOIN Lieu AS l
			ON l.Lieu_ID = s.ville
		WHERE s.id = '.$id.' 
		LIMIT 1 
	');

	if( $do = fetch($donne) )
	{
		$init =  array(
			'id' => $do['id'], 
			'numero' => $do['numero'],
			'nom' => $do['nom'],
			'logo' => $do['logo'],
			'adresse' => array( 
				'ville' => array(
					'id' => $do['id_ville'], 
					'nom' => $do['ville'], 
					'cp' => $do['cp'], 
					'dep' => array( 'num' => $do['dep'] ), 
				),
				'rue' => $do['adresse'],
			), 
			'type' => $do['type'],
			'date' => $do['date'],
			'desc' => $do['description'],
			'mail' => $do['email'],
			'mail_rq' => $do['mail_rq'],
			'actif' => $do['actif'], 
			'code_externe' => $do['code_externe'],
			'conv' => $do['conv'], 
			'payant' => (bool)$do['payant'], 
			'id_paypal' => $do['id_paypal'],
			'date_fin_adhesion' => $do['date_fin_adhesion'], 
			'rappel' => $do['rappel'], 
			'rappel_facture' => $do['rappel_facture'] 
		); 

		$banniere = req('SELECT URL, Image FROM Bandeaux b WHERE id_structure='.$id.' 
			AND Type LIKE(\'banniere\') 
			LIMIT 1 '); 

		if( $ban = fetch($banniere ) )
		{
			$init['banniere'] = $ban['Image']; 
			$init['banniere_url'] = $ban['URL']; 
		}

		$str = new structure($init); 

		$donne = req('SELECT titre, id, tel, site FROM structure_contact WHERE id_structure='.$id ); 
			
		while($do = fetch($donne) )
		{
			$str->ajt_contact( $do ); 
		}
		return $str;
	}
	else
	{
		return FALSE; 
	}
}

function nbe_saisi_annee()
{
	$donne = req('
	SELECT COUNT(*) nbe
	FROM Evenement e
	WHERE ( Creat_datetime BETWEEN \''.date('Y').'-01-01\' AND \''.date('Y').'-12-31\' )
	AND Contact_id IN ( 
		SELECT id 
		FROM structure_contact
		WHERE id='.absint($this->id).'
	)
	'); 
		
	$do = fetch($donne); 
	return (int)$do['nbe']; 
}

function str_nbe_a_venir($str)
{
	if( $str !== FALSE )
	{
		$donne = req('
		SELECT COUNT(*) nbe 
		FROM ( 
			SELECT e.id
			FROM Evenement e
			LEFT JOIN Evenement_dates ed
				ON ed.Evenement_id = e.id 
			WHERE Evenement_Date >= NOW() 
			AND Contact_id IN ( 
				SELECT id 
				FROM structure_contact
				WHERE id_structure='.absint($str->acc_id() ).'
			)
			GROUP BY e.id 
		) AS t
		'); 
			
		$do = fetch($donne); 
		return (int)$do['nbe']; 
	}

	return 0; 
}


function str_relais_enr($str)
{
	$relais = $str->acc_id() == 0; 
	str_enr($str); 

	if( $relais )
	{
		$loc = new location ;
		$loc->lsstr = array( $str->acc_id() ); 
		$loc->id_structure = $str->acc_id(); 
		$loc->nom = $str->acc_nom(); 
		$loc->css = 'page'; 
		$loc->enr(); 
		str_location_choix($str, $loc->code); 
	}
}
