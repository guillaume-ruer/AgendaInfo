<?php
require_once C_INC.'evenement_class.php'; 
require_once C_INC.'historique_class.php'; 

function event_init($id)
{
	static $meta =NULL, $date, $lieu; 

	if( is_null($meta ) )
	{
		$meta = prereq('
			SELECT e.id, e.Cat_id categorie__id, cat.CAT_IMG categorie__img, 
				e.Contact_id contact__id, e.image, e.public, 
				e.Actif etat, e.Creat_id id_createur, e.Creat_datetime date_creation, 
				e.lei id_externe, ed.Titre as titre, ed.Description `desc`,
				c.site contact__site, c.titre contact__titre, c.tel contact__tel, e.tarif tarif__id, 
				s.id contact__structure__id, s.nom contact__structure__nom, 
				s.email contact__structure__mail, s.mail_rq contact__structure__mail_rq, 
				s.actif contact__structure__actif, e.type, e.affiche, e.affiche_url,
				e.source, e.lei id_externe, e.date_maj
			FROM Evenement e
			LEFT JOIN Evenement_details ed
				ON e.id = ed.Evenement_id 
			LEFT JOIN structure_contact c 
				ON e.Contact_id = c.id
			LEFT JOIN structure s
				ON s.id = c.id_structure
			LEFT JOIN Categories cat 
				ON e.Cat_id = cat.CAT_ID 
			WHERE e.id=?
			AND ed.Langue_id = 1
			LIMIT 1 '); 
		$date = prereq('
			SELECT Evenement_Date date
			FROM Evenement_dates 
			WHERE Evenement_id =?  ');
		$lieu = prereq('
			SELECT el.Lieu_id id, Lieu_Ville ville, Lieu_Dep dep__num
			FROM Evenement_lieux el 
			LEFT JOIN Lieu 
				ON Lieu.Lieu_ID = el.Lieu_id
			WHERE el.Evenement_id =?  ');
	}

	//"Métadonné"
	exereq( $meta, array($id) ); 
	$do = fetch($meta); 

	//Si on ne trouve rien on arrête 
	if(empty($do) )
	{
		return FALSE;
	}

	$event = new evenement( genere_init($do) ); 

	//Dates 
	exereq($date, array( $event->acc_id() ) ); 
	while($do = fetch($date ) )
	{
		$event->ajt_date($do['date']); 
	}

	//Lieux
	exereq($lieu, array( $event->acc_id() ) ); 
	while($do = fetch($lieu) )
	{
		$event->ajt_lieu( new ville( array(
			'id' =>$do['id'], 
			'nom' =>$do['ville'],
			'dep' => ['num' => $do['dep__num'] ]
		) )); 
	}

	return $event; 
}

function event_txt($e)
{
	$event = ''; 
	if( $e->acc_tab_lieu() )
	{
		$event = ' ('.count($e->acc_tab_lieu() ).' Lieu(x) )';
	}

	$event .="\nEtat : ".evenement::$TAB_ETAT[ $e->acc_etat() ];

	if( $e->acc_tab_date() )
	{
		$event .= "\n\nDate(s) :";

		foreach($e->acc_tab_date() as $date )
		{
			$event.= "\n\t$date"; 
		}
	}

	return $event; 
}

function event_insert_commentaire($event, $idutr, $com, $type )
{
	static $ins_comm=NULL;
	if(is_null($ins_comm ) )
	{
		$ins_comm = prereq('INSERT INTO historique(idevent, idutr, date, commentaire, evenement, type, titre, description, etat ) 
			VALUES(?,?,?,?,?, ?,?,?,? )');
	}

	exereq($ins_comm, array( $event->acc_id(), $idutr, time(), $com, event_txt($event), 
		$type, $event->acc_titre(), $event->acc_desc(), $event->acc_etat() ) );
	$id = derid(); 
	return $id;
}

function event_historique($e)
{
	static $sel_historique=NULL;
	if(is_null($sel_historique ) )
	{
		$sel_historique = prereq('
			SELECT h.idevent ide, h.commentaire com, h.date, h.idutr idu, u.User pseudo, h.evenement, h.description, h.titre
			FROM historique h 
			LEFT JOIN Utilisateurs u 
				ON u.id = h.idutr
			WHERE idevent=? 
			ORDER BY date DESC 
		'); 
	}

	exereq($sel_historique, array($e->acc_id() ) );
	$tab = array(); 

	while( $do = fetch($sel_historique ) )
	{
		$tab[] = array(
			'ide' => absint($do['ide']),
			'idu' => absint($do['idu']),
			'com' => nl2br(secuhtml($do['com']) ),
			'date' => madate($do['date']),
			'pseudo' => secuhtml($do['pseudo']),
			'event' => nl2br(str_replace("\t", str_repeat('&nbsp;', 4 ), $do['evenement']) ),
			'titre' => secuhtml($do['titre']),
			'desc' => secuhtml($do['description'])
		); 
	}

	return $tab; 
}

function event_der_modif($e)
{
	$donne = req('
		SELECT h.idevent ide, h.commentaire com, h.date, h.idutr idu, u.User pseudo, h.evenement, h.description, h.titre
		FROM historique h 
		LEFT JOIN Utilisateurs u 
			ON u.id = h.idutr
		WHERE idevent='.(int)$e->acc_id().' 
		ORDER BY date DESC 
		LIMIT 2 
	'); 

	if( fetch($donne) && ($do = fetch($donne) ) )
	{
		return array(
			'ide' => absint($do['ide']),
			'idu' => absint($do['idu']),
			'com' => $do['com'],
			'date' => madate($do['date']),
			'pseudo' => $do['pseudo'],
			'event' => $do['evenement'],
			'titre' => $do['titre'],
			'desc' => $do['description']
		); 
	}
	else
	{
		return FALSE; 
	}
}

function event_ins($e, $id_utilisateur, $com='')
{
	static $insert_event=NULL; 

	if(is_null($insert_event) )
	{
		$insert_event = prereq('
			INSERT INTO Evenement (Cat_id, Contact_id, Actif, Creat_id, Creat_datetime, 
				Aleat, lei, source, date_maj, tarif, image, public, type, affiche, affiche_url ) 
			VALUES(:id_cat, :id_contact, :etat, :id_creat, NOW(), 
				:aleat, :lei, :source, :date_maj, :tarif, :image, :public, :type, :affiche, :affiche_url ) 
		');
	}

	//Il semble que ça sert à mettre les événement n'ayant plus qu'une date en premier. 
	$aleat = (count($e->acc_tab_date() )>1) ? rand(999999999,99999999999) : rand(0,999999999);
	$cat = $e->acc_categorie()->acc_id(); 

	//Insertion des "métadonnée" 
	exereq($insert_event, array(
		'id_cat' => $cat == 0 ? NULL : $cat,
		'id_contact' => ($i = $e->acc_contact()->acc_id() )==0? NULL : $i, 
		'etat' => $e->acc_etat(), 
		'id_creat' => $id_utilisateur, 
		'aleat' => $aleat, 
		'lei' => $e->acc_id_externe(), 
		'source' => $e->acc_source(), 
		'date_maj' => $e->acc_date_maj(), 
		'tarif' => $e->acc_tarif()->acc_id(),
		'image' => $e->acc_image(), 
		'public' => $e->acc_public()->acc_id(), 
		'type' => $e->acc_type(), 
		'affiche' => $e->acc_affiche(), 
		'affiche_url' => $e->acc_affiche_url(), 
	) );

	mess('Nouvel événement créé'); 
	//Récupération de l'identifiant nouvellement crée 
	$e->mut_id(derid() );

	//on fait le ménage avant d'inséré un nouveau événement 
	$e = event_ins_date($e);
	$e = event_ins_lieu($e); 
	$e = event_ins_detail($e);

	event_insert_commentaire($e, $id_utilisateur, $com, historique::AJOUT); 
	return $e; 
}

function event_ins_detail($e)
{
	static $insert_detail=NULL, $sup_detail=NULL; 

	if(is_null($insert_detail) )
	{
		$sup_detail = prereq('DELETE FROM Evenement_details WHERE Evenement_id=:id  ');
		$insert_detail = prereq('
			INSERT INTO Evenement_details( Titre, Description, Evenement_id, Langue_id, Verif)
				VALUES(:titre, :desc, :id, 1, 1 )
		');
	}

	//Insertion des détails 
	exereq( $sup_detail, array('id' => $e->acc_id() ) ); 
	exereq($insert_detail, array(
		'titre' => $e->acc_titre(),
		'desc' => $e->acc_desc(), 
		'id' => $e->acc_id()
	));

	return $e; 
}

function event_ins_lieu($e)
{
	static $insert_lieu=NULL, $sup_lieu=NULL;

	if(is_null($insert_lieu) )
	{
		$sup_lieu = prereq('DELETE FROM Evenement_lieux WHERE Evenement_id=:id  ');
		$insert_lieu = prereq('
			INSERT INTO Evenement_lieux(Evenement_id, Lieu_id ) 
				VALUES(:id_event, :id_lieu )
		'); 
	}

	exereq( $sup_lieu, array('id' => $e->acc_id() ) ); 
	foreach($e->acc_tab_lieu() as $lieu )
	{
		exereq($insert_lieu, array(
			'id_event' => $e->acc_id(),
			'id_lieu' => $lieu->acc_id(), 
		) );
	}
	return $e; 
}

function event_ins_date($e)
{
	static $insert_date=NULL, $sup_date=NULL; 

	if(is_null($insert_date) )
	{
		$sup_date = prereq(' DELETE FROM Evenement_dates WHERE Evenement_id=:id ');
		$insert_date = prereq('
			INSERT INTO Evenement_dates(Evenement_id, Evenement_date)
				VALUES(:id_event, :date) 
		'); 
	}

	//Préparation de la requête des dates 
	exereq( $sup_date, array('id' => $e->acc_id() ) ); 
	foreach($e->acc_tab_date() as $date )
	{
		if( est_date($date) )
		{
			exereq( $insert_date, array(
				'id_event' => $e->acc_id(), 
				'date' => $date 
			) );
		}
	}
	return $e; 
}

function event_maj($e, $id_utilisateur, $com='' )
{
	static $maj_event = NULL, $maj_event_img; 

	if(is_null($maj_event) )
	{
		$maj_event = prereq('
			UPDATE Evenement
			SET Cat_id = :id_cat, Actif=:etat, Contact_id=:id_contact, tarif=:tarif,
				public=:public, der_historique=:dh, image=:image, affiche=:affiche, affiche_url=:affiche_url, 
				type=:type, date_maj=:date_maj
			WHERE id=:id 
			LIMIT 1 
		');
	}

	$idh = event_insert_commentaire($e, $id_utilisateur, $com, historique::MODIF ); 

	$tab_maj = array(
		'id_contact' => ( ($i=$e->acc_contact()->acc_id() )==0 ) ? NULL: $i , 
		'id_cat' => ( $cat = $e->acc_categorie()->acc_id() )== 0 ? NULL : $cat , 
		'etat' => $e->acc_etat(), 
		'id' => $e->acc_id(),
		'tarif' => $e->acc_tarif()->acc_id(),
		'public' => $e->acc_public()->acc_id(), 
		'dh' => $idh, 
		'image' => $e->acc_image(), 
		'affiche' => $e->acc_affiche(), 
		'type' => $e->acc_type(), 
		'affiche_url' => $e->acc_affiche_url(), 
		'date_maj' => $e->acc_date_maj()
	); 

	exereq( $maj_event, $tab_maj );

	mess('L\'événement a été mis à jour.'); 

	$e = event_ins_date($e);
	$e = event_ins_lieu($e); 
	$e = event_ins_detail($e); 
	return $e; 
}

function event_enr($e, $com='', $id_utilisateur=ID )
{
	if( $e->acc_id() == 0 )
	{
		$e = event_ins($e, $id_utilisateur, $com);
	}
	else
	{
		$e = event_maj($e, $id_utilisateur, $com); 
	}

	return $e; 
}

// Le ménage
function event_supp_date_lieu_detail()
{
	static $sup_date, $sup_lieu, $sup_detail; 

	if(is_null($sup_date) )
	{
		$sup_date = prereq(' DELETE FROM Evenement_dates WHERE Evenement_id=:id ');
		$sup_lieu = prereq('DELETE FROM Evenement_lieux WHERE Evenement_id=:id  ');
		$sup_detail = prereq('DELETE FROM Evenement_details WHERE Evenement_id=:id  ');
	}

	exereq( $sup_date, array('id' => $this->id ) ); 
	exereq( $sup_lieu, array('id' => $this->id ) ); 
	exereq( $sup_detail, array('id' => $this->id ) ); 
}

function event_supp($id, $sup_mode=0 )
{	
	switch($sup_mode )
	{
		case 0 : 
			req('UPDATE Evenement SET Actif=2 WHERE id='.$id.' ');
		break; 
		case 1 : 
		break;
	}
}

function event_maj_etat( $id, $etat, $idutr, $com )
{
	static $req = NULL; 
	global $MEMBRE; 

	if( is_null($req) )
	{
		$req = prereq('UPDATE Evenement SET Actif=:etat WHERE id=:id LIMIT 1'); 
	}

	// Vérif etat 
	if( !evenement::est_etat($etat) )
	{
		return FALSE; 
	}

	// Vérif id 
	if( is_numeric($id) )
	{
		$id = array($id); 
	}
	elseif( !(is_array($id) && !empty($id) ) )
	{
		return FALSE; 
	}

	foreach( $id as $i )
	{
		exereq( $req, array('etat' => $etat, 'id' => $i ) );
		if( $req->rowCount() > 0 )
		{
			event_insert_commentaire(new evenement( array( 'id' => $i, 'etat' => $etat) ), $idutr, $com, historique::MODIF ); 
		}
	}

	return TRUE; 
}

function event_membre_droit($ide, $membre)
{
	
	if( droit(GERER_EVENEMENT) )
	{
		$valide = TRUE;
	}
	else
	{
		$donne = req($var = 'SELECT Contact_id FROM Evenement WHERE id='.(int)$ide.' LIMIT 1');
		$do = fetch($donne); 
		$cid = $do['Contact_id']; 

		$donne = req($var = 'SELECT IF(
			('.$cid.') IN (
				SELECT sc.id 
				FROM structure_contact sc 
				LEFT JOIN structure_droit sd
					ON sc.id_structure = sd.structure
				WHERE sd.utilisateur = '.(int)$membre->id.'
				AND sd.droit & '.STR_EVENEMENT.'
			)
			OR '.$cid.' IN( SELECT sc.id FROM structure_contact sc WHERE sc.id_structure='.(int)$membre->id_structure.' )
			,1,0) AS res
		');
		$do = fetch($donne);  
		$valide = (bool)$do['res']; 
	}
	
	return $valide; 
}
