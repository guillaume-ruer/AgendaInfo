<?php
// Fichier a modifier 
function stat_lei()
{
	$tab = array(0,0,0,0);
	$donne = req('SELECT Actif, COUNT(*) AS nb FROM Evenement WHERE lei != 0 GROUP BY Actif ');
	while( $do = fetch($donne) )
	{
		$tab[ (int)$do['Actif'] ] = (int)$do['nb'];
	}

	req('INSERT INTO stat_lei ( time, supprime, actif, masque ) 
		VALUES ( '.time().' , '.$tab[2].', '.$tab[1].', '.$tab[0].' ) ');

}

function tps($t) 
{
	return round( (microtime(TRUE) - $t ) * 1000, 2 ); 
}

function mon_echo($var )
{
	if(is_array($var ) )
	{
		foreach($var as $val ) 
		{
			mon_echo($val);
		}
	}
	else
	{
		echo nl2br(htmlspecialchars($var, ENT_QUOTES, 'UTF-8').' '); 
	}
}

function impr($truc)
{
	echo '<pre>';
	print_r($truc);
	echo '</pre>';
}

function my_mktime($date)
{
	$tab_au = explode('/', $date);
	return mktime(0,0,0, $tab_au[1], $tab_au[0], $tab_au[2]);
}

function etat($mess )
{
	global $tab_mess;
	$tab_mess[] = '['.$mess.']'; 
}

function retat()
{
	global $tab_mess; 
	return implode("\n", $tab_mess); 
}

function mon_hash($tab_donne )
{
	$concat = '';
	foreach($tab_donne as $do )
	{
		$concat .= (is_array($do) ) ? implode(',', $do) : $do; 
	}
	
	return sha1($concat);
}

function format_date_infolimo($date)
{
	//jour/moi/anné -> anné-moi-jour
	return preg_replace('`([0-9]+)/([0-9]+)/([0-9]+)`', '$3-$2-$1', $date );
}


function sup_lei()
{
	req('UPDATE Evenement SET Actif=2, Last_mod_datetime=NOW(), Last_mod_id=414  WHERE lei!=0 AND der_verif + (3580*24) < '.time().' ');
}
function import($nump)
{
	global $BDD, $tab_mess; 

	$time_lei = 'time_lei_cron_'.$nump; 

	if(rappel($time_lei) > time() )
	{ 
		exit(); 
	}

	memor($time_lei, time()+(3500 ) ); 

	/*
		Script : 
	*/

	include '../include/conf.php'; 
	include '../include/fonc_sql.php';

	try
	{
		$BDD= new PDO('mysql:host='.$host.';dbname='.$bdd, $utilisateur , $mdp );
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}

	unset($host, $bdd, $utilisateur, $mdp); 

	req('Set Charset utf8');

	$import = 1;
	$save = 0;
	/*
		Traitement des données. 
	*/

	//Préparation des requete de recherche 
	$pre_lieu = $BDD->prepare('SELECT Lieu_ID AS id FROM Lieu WHERE Lieu_Ville LIKE ? '); 
	$pre_event = $BDD->prepare('SELECT hash,id FROM Evenement WHERE lei = ? '); 

	//Préparation des requete d'insertion de l'évenement 
	$ins_event = $BDD->prepare('INSERT INTO Evenement(Cat_id, lei, hash, Aleat, Actif, Creat_datetime, Contact_id, der_verif) VALUES( ?,?,?,?,0,NOW(), 638, '.time().' ) '); 
	$ins_detail = $BDD->prepare('
		INSERT INTO Evenement_details( Evenement_id, Description, Titre, Langue_id ) 
				VALUES( ?, ?,? ,1 ) '); 
	$ins_date = $BDD->prepare('INSERT INTO Evenement_dates(Evenement_id, Evenement_Date ) VALUES (?,?) ');
	$ins_lieu = $BDD->prepare('INSERT INTO Evenement_lieux(Evenement_id, Lieu_id) VALUES (?,?) ');

	//Préparation pour l'Update 
	$sup_date = $BDD->prepare('DELETE FROM Evenement_dates WHERE Evenement_id=?');
	$sup_lieu = $BDD->prepare('DELETE FROM Evenement_lieux WHERE Evenement_id=?');

	//Update 
	$up_details = $BDD->prepare('
		UPDATE Evenement_details 
		SET Description = ?, Titre = ? 
		WHERE Evenement_id=?
		AND Langue_id = 1  
		LIMIT 1
	');

	$up_evenement = $BDD->prepare('
		UPDATE Evenement 
		SET Actif = 0, Cat_id = ?, hash = ?, der_verif = ?, Last_mod_datetime = NOW(), Last_mod_id=414
		WHERE lei = ? 
		LIMIT 1 
	');

	$up_der_verif = $BDD->prepare('
		UPDATE Evenement 
		SET der_verif = ? , hash=? 
		WHERE lei = ? 
		LIMIT 1 
	');


	//Tableau des corrspondances thematique 
	$tab_id_theme = array(
		'Festivals' => 75,
		'Randonnées organisées' => 36,
		'Vide-greniers, marchés aux puces et brocantes' => 25,
		'Foires et marchés' => 1,
		'Expositions' => 26,
		'Stages à thèmes' => 54,
		'Fêtes locales' => 75, 
		'Concerts et animations musicales' => 3,
		'Courses pédestres' => 36,
		'Régates' => 16,
		'Tournois sportifs' => 126,
		'Salons' => 26, 
		'Courses cyclistes' => 21, 
		'Fêtes votives' => 75, 
		'Concours de boules' => 29, 
		'Animations artistiques' => 26,
		'Marchés des producteurs de pays' => 1,
		'Rencontres sportives' => 126, 
		'Courses motorisées' => 126, 
		'Littérature' => 82,
		'Multi-activités' => 54,
		'Spectacles vivants' => 154, 
		'Animations jeunesse' => 14, 
		'Sorties découverte de la nature' => 6, 
		'Conférences - Projections cinématographiques' => 13
	);

	/*
		Les donnée à concaténé dans le champs description
		Certain champs peuvent être ajouté ici pour signalé au validateur des champs manquants ou autre 
	*/
	$tab_concat = array('COMMENTAIRE','COMMENTAIREL1','COMMENTAIREL2','ADRPROD_TEL','ADRPROD_URL', 'ADRPROD_COMPL_ADRESSE' );

	$interval = 500; 
	$max = $nump + 6;
	for($p = $nump; $p < $max; $p ++ )
	{
		$f = ($p == 0 ) ? 1 : $p* $interval+ 1 ;
		$t = ($p == 0 ) ? $interval  : $interval * $p + ($interval);
		$url = 'http://www.tourisme-limousin.net/applications/xml/exploitation/listeproduits.asp?rfrom='.$f.'&rto='.$t.'&user=2000033&pwkey=3a2b967c716113be7ecf4120501c031e&urlnames=tous&PVALUES=30000006%2C21%2F06%2F2010+00%3A00%3A00%2C21%2F09%2F2010+23%3A59%3A59%2C21%2F06%2F2010+00%3A00%3A00%2C01%2F01%2F2100+23%3A59%3A59&PNAMES=elgendro%2Cvalidaddu%2Cvalidadau%2Chorariodu%2Chorarioau&clause=2000033000006';


		/*
			Récupération des donnée dans un tableau php
		*/

		$doc = new DomDocument(); 
		$doc->load($url); 

		$elements = $doc->getElementsByTagName('sit_liste'); 
		//Le tableau des balise 
		$tab_balise = array();
		//Le tableau des balise trouvé, avec des champs par défaut remplis avec les donnée transformé. 
		$tab_bal = array('etat','date',  'concat', 'lieu','theme', 'hash'   ); 
		//Les balise ignoré pendant la premiere analyse. 
		$ignore = array(
			'LONGITUDE',
			'VALABLE_DEPUIS',
			'VALABLE_JUSQU_A',
			'HORAIRES',
			'#text',
			'DATMAJ',
			'PREST_NADRESSE'
		);

		/*
			Premiere analyse 
			Le but étant de mettre ensemble les dates des produit, si il y a lieu de le faire 
		*/
		$i = 0;
		while( ($element = $elements->item($i) ) != NULL )
		{
			$i++;
			//Les enfant de sit_list 
			$enfants = $element->childNodes;

			//Tableau temporaire qui sera dans tab_balise 
			$tmp = array(); 
			//L'id du produit, sera l'index du tableau tab_balise 
			$id = '';
					
			//Parcour des balises enfant 
			foreach($enfants as $enfant)
			{
				//Le <nom>
				$nom = $enfant->nodeName;

				//On passe à sit_liste suivante si on est sur une visite guidé ou dans un département exterieur au limousin 
				if( $nom == 'TYPE_NOM'  AND preg_match('`visites? guidé`i', $enfant->nodeValue) )
				{
					continue 2;		
				}
				elseif( $nom == 'ADRPROD_CP' AND !preg_match('`^(19|87|23)`', $enfant->nodeValue ) )
				{
					continue 2;		
				}

				//Ajout dans un tableau pour les donnée des <DU> et <AU> car il peut en y avoir plusieurs 
				if($nom == 'DU' OR $nom == 'AU' )
				{
					$tmp[$nom][] = $enfant->nodeValue; 
				}
				else
				{
					$tmp[$nom] = $enfant->nodeValue; 
				}

				//Mise à jour de tab_bal si c'est une balise qu'on ignore pas ou qui n'y est pas encore 
				if(!in_array($nom, $tab_bal ) AND !in_array($nom, $ignore) )
				{	
					$tab_bal[] = $nom;
				}
				
				//On retient l'id du produit (clé de tab_balise )
				if($nom == 'PRODUIT' )
				{
					$id = $enfant->nodeValue;
				}
					
			} 
			
			//On a déjà le produit, on met à jour les tableau des dates 
			if(isset($tab_balise[$id] ) )
			{
				$tab_balise[ $id ][ 'DU'][] = $tmp['DU'][0]; 
				$tab_balise[ $id ][ 'AU'][] = $tmp['AU'][0]; 
			}
			else
			{
				//Sinon, on ajout le tableau tmp 
				$tab_balise[ $id ] = $tmp; 
			}
		}


		/*
			Deuxieme boucle
			Vérification des dates, lieu etc. 
			On ignore des produit si on a pas d'élément exploitable
		*/
		foreach($tab_balise as $id => $donne_prod )
		{

			//Suppression des variables genere precedement
			foreach($tab_bal as $nom )
			{
				unset(${$nom}); 
			}

			extract($donne_prod); 
			$pas_pb = TRUE;
			$tab_mess = array(); 

			
			/*
				TRAITEMENT SUR LES DATES 
			*/
			//Tableau des dates à entré automatiquement en bdd 
			$tab_date = array(); 

			//Nombre de date DU et AU 
			$nb_du = count($DU);
			$nb_au = count($AU); 

			//Si on a pas le même nombre de date, on shoot l'event
			if($nb_du != $nb_au )
			{
				etat('Supprimé');
				etat('Pas le même nombre de DU et AU');
				$pas_pb = FALSE;
			}

			//Si le nombre de date est supérieur à 1 
			if($nb_du > 1 )
			{
				//On vérifie que chaque DU et AU soit égaux, sinon on shoot l'event
				for($i = 0; $i < $nb_du; $i ++ )
				{
					if($DU[ $i ] == $AU[ $i ])
					{
						//On ajoute la date à ajouté automtiquement dans le cas ou les dates sont égau
						$tab_date[] = format_date_infolimo($DU[$i]); 
					}
					else
					{
						etat('Plusieurs date, mais pas égaux.');
						etat('Evenement supprimé ! ');
						$pas_pb = FALSE;
						break;
					}
				}
			}
			else
			{
				//Si le nombre de date est égale à un, on vérifi l'intervale	
				$time_du = my_mktime($DU[0]);
				$time_au = my_mktime($AU[0]);

				$ecart = $time_au - $time_du;
				
				//Plus de 300 Jour d'écart, on ignore 
				if($ecart > 3600 * 24 * 300 )
				{
					etat('evenement supprimé');
					etat('Près d\'un ans d\'ecart.'); 
					$pas_pb = FALSE;
				}
				elseif($ecart > 3600* 24 * 40 )
				{
					//40 jour d'écart, on garde, mais on entre pas automatiquement les dates 
					etat('ecart entre les deux date trop important, ajout mais pas de date');
				}
				else
				{
					//Sinon, on ajoute tout automatiquement en bdd (transformer au format de info-limousin )
					for($i = $time_du; $i <= $time_au; $i+=(24*3600 ) )
					{
						$tab_date[] = date('Y-m-d', $i ); 
					}
				}
			}

			if( empty($COMMENTAIRE) AND empty($COMMENTAIREL1) AND empty($COMMENTAIREL2) )
			{
				etat('Description vide, Evenement supprim�'); 
				$pas_pb = FALSE;
			}

			//Ajout des donné à exploité
			$tab_balise[ $id ][ 'date' ] = $tab_date; 

			/*
				RECHERCHE DE L'ID DU LIEU 
			*/

			//Transformation pour trouvé le nom dans la base d'info-limousin
			$r_lieu = str_replace(' ', '-', $ADRPROD_LIBELLE_COMMUNE);
			$r_lieu = str_ireplace( array('Saint', 'saints', 'st', 'ste' ), 's%', $r_lieu);
			$r_lieu = str_ireplace( array('l\'' ), '%', $r_lieu);
			$pre_lieu->execute(array( $r_lieu ) ); 
			$lieu = '';

			//On a un seul résultat alors on l'ajoute
			if($pre_lieu->rowCount() == 1)
			{
				$do = fetch($pre_lieu); 
				$lieu = $do['id'];
				$tab_balise[$id]['lieu'] = $lieu; 
			}
			else
			{
				//On a plusieur ou pas de résultat, alors on ajoute les donnée du lei dans le champ description pour le validateur 
				etat('Le lieu n\'a pas été trouvé : '.$ADRPROD_LIBELLE_COMMUNE.' cp : '.$ADRPROD_CP );
			}


			/*
				RECHERCHE id du theme 
			*/

			//On fait correspondre le TYPE_NOM Avec notre tableau de theme, pour trouvé notre id. 
			if(isset($tab_id_theme[ $TYPE_NOM ] ) ) 
			{
				$theme =  $tab_id_theme[ $TYPE_NOM ]; 
			}
			else
			{
				//Ajout dans le tableau pour les donnée à entré dans le champ description 
				$theme = ''; 
				etat('Le thème n\' a pas été trouvé : '.$TYPE_NOM ); 
			}

			$tab_balise[$id]['theme'] = $theme; 

			/*
				CONCATENATION DES COMMENTAIRES 
			*/

			//on crée le contenu du champs description 
			$tab_balise[ $id ]['concat' ] = '';

			foreach($tab_concat as $nom )
			{
				$tab_balise[ $id ]['concat' ] .= (isset(${$nom}) ) ? ${$nom} : '' ; 
				$tab_balise[ $id ]['concat' ] .= "\n"; 
			}

			//Avec les message d'etat
			$concat = $tab_balise[$id ]['concat'].retat();


			/*
				CREATION DU HASH 
			*/
			
			//On ignore les dates dans le hash
			unset($donne_prod['DU'], $donne_prod['AU']);
			$hash = mon_hash($donne_prod); 
			$tab_balise[ $id ]['hash'] = $hash; 

			/*
				INSERTION OU UPDATE EN BDD 
			*/

			if($pas_pb )
			{
				//Est ce que le produit est déjà présent en bdd? 
				$pre_event->execute(array($PRODUIT) ); 

				//Oui 
				if($pre_event->rowCount() == 1 )
				{
					//Vérification du hash. Si différent, update 
					$do = fetch($pre_event); 
					$ide = (int)$do['id'];

					if(!empty($do['hash']) AND $hash != $do['hash'] )
					{
						etat('Update');

						//Update 
						//Suppression des dates 
						$sup_date->execute(array($ide) );  
						//Suppression des lieu 
						$sup_lieu->execute(array($ide) );  
						//Mise à jour des détails 
						$up_details->execute(array($concat, $NOM, $ide) );  
						//Mise à jour de l'évenement 
						$up_evenement->execute(array($theme, $hash, time(), $PRODUIT) ) ;
						
						//Réinsertion des dates 
						if(!empty($tab_date) )
						{
							foreach($tab_date as $date )
							{
								$ins_date->execute( array($ide, $date ) );
							}
						}
						
						//Réinsertion du lieu 
						if(!empty($lieu) )
						{
							$ins_lieu->execute(array($ide, $lieu ) ); 
						}

					}
					else
					{
						etat('Pas de modif') ;
						$up_der_verif->execute(array(time(), $hash,  $PRODUIT) ); 
					}



				}
				elseif($pre_event->rowCount() == 0 )
				{
					//Le produit n'est pas présent alors on l'insert 

					$aleat = ( count($tab_date) > 1 ) ?  rand(999999999,99999999999) : rand(0,999999999);

					$ins_event->execute(array($theme, $PRODUIT, $hash,$aleat  ) );
					$ide = $BDD->lastInsertId(); 
					$ins_detail->execute( array($ide, $concat, $NOM ) );

					if(!empty($tab_date) )
					{
						foreach($tab_date as $date )
						{
							$ins_date->execute( array($ide, $date ) );
						}
					}
					
					if(!empty($lieu) )
					{
						$ins_lieu->execute(array($ide, $lieu ) ); 
					}

					etat('Inséré');
				}
			}

			$tab_balise[ $id ]['etat'] = retat(); 
		}

		echo 'De : '.$f.' à '.$t." [ok]\n "; 
		sleep(2);
	}
	
	sup_lei(); 
	echo "Suppression des donné non vérifié depuis plus de 24h [ ok ]\n ";

	stat_lei();

	echo "Creation du log [ok] "; 
}
