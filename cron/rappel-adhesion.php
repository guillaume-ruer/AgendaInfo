<?php
require_once '../include/init.php'; 
require_once C_INC.'fonc_memor.php'; 
require_once C_INC.'structure_class.php';
require_once C_INC.'facture_pdf_fonc.php'; 
require_once C_INC.'structure_facture_fonc.php'; 
require_once C_INC.'courriel_class.php'; 

// Conf
$verbeu = TRUE; 

function entre( $var, $deb, $fin )
{
	return ( $deb <= $var && $var <= $fin ); 
}

/*
	Toute les structures dont la date de fin d'adhesion 
	arrive dans 1 mois 
	ou est dépassé depuis 3 mois. 
*/
$donne = req('
	SELECT id, nom, date_fin_adhesion, actif, type, conv, payant, email mail, rappel, rappel_facture
	FROM structure 
	WHERE '.time().' BETWEEN (date_fin_adhesion-24*3600*30) AND (date_fin_adhesion+24*3600*31*4) 
	AND payant=1
	AND actif!=0
'); 

/*
	Les utilisateurs attaché à une structure 
*/
$pre_gerant = prereq('
		SELECT u.*, IF( sd.rappel IS NULL, 1, sd.rappel) AS rappel 
		FROM Utilisateurs u
		LEFT OUTER JOIN structure_droit sd
			ON sd.utilisateur = u.id AND sd.structure=:str
		WHERE id_structure=:str
	UNION
		SELECT u.*, sd.rappel
		FROM Utilisateurs u
		JOIN structure_droit sd
			ON sd.utilisateur = u.id 
		WHERE sd.structure=:str
');

$str_maj = prereq('UPDATE structure SET rappel=? WHERE id=?');
$str_maj_fact = prereq('UPDATE structure SET rappel=?, rappel_facture=? WHERE id=?');
$str_maj_des = prereq('UPDATE structure SET actif=2, rappel=? WHERE id=?');

$def = include C_ADMIN.'structure/include/mail-defaut.php'; 
$phrase = rappel('mail-rappel', $def); 


ob_start(); 
?>
<!DOCTYPE html>
<html>
<head>

<style>
body
{

	font-size:90%; 
}

table
{
	border-collapse:collapse;
}

td
{
	border:1px solid black; 
	padding:5px; 
}

table tr:nth-child(odd)
{
	background:#dddddd; 
}
</style>
</head>
<body>
<table>
<?php 
while($do_str = fetch($donne) ) :
	$str = new structure($do_str); 
	$etape = 'inconnu'; 	
	$jour = (int)( (time() - $str->acc_date_fin_adhesion())/(24*3600) ); 
	$sujet = '';
	$mail = ''; 
	$dos = ''; 
	$dof = ''; 
	$rf = ''; 
	$envoie_mail = FALSE; 

	if( entre($jour, -30, 0) )
	{
		// Rappel 
		if( $do_str['rappel'] != 1 )
		{
			$etape = 'Rappel'; 
			$rappel = 1; 
			$mail = $phrase[0][1];
			$sujet = $phrase[0][2];
			$envoie_mail = TRUE; 

			exereq($str_maj, [$rappel, $str->acc_id() ]); 
		}
		else
		{
			$etape = 'Rappel : déjà effectué'; 
		}
	}
	elseif( entre($jour, 1, 30) )
	{
		if( $do_str['rappel'] != 2 )
		{
			// Rappel + facture 
			// Sauvegarder nom de la facture. 

			$dos = structure_facture_dossier(); 
			$dof = genere_facture_pdf($str->acc_id(), $dos); 
			$rf = str_replace('../', '', $dof['chenr']); 
			$envoie_mail = TRUE; 

			$etape = 'Adhésion + facture'; 
			$rappel = 2; 

			$mail = $phrase[1][1];
			$sujet = $phrase[1][2];

			exereq($str_maj_fact, [$rappel, $rf, $str->acc_id()] ); 
		}
		else
		{
			$etape = 'Adhésion + facture : déjà éfféctué'; 
		}
	}
	elseif( entre($jour, 31, 60 ) )
	{
		if( $do_str['rappel'] != 3 )
		{
			// Relance + facture + annonce désactivation de saisis dans un mois. 
			// Si facture sauvegarder, l'utiliser, sinon, la générer. 
			$envoie_mail = TRUE; 
			$etape = "Relance + facture + annonce désactivation de saisis dans deux mois."; 
			$rappel = 3; 

			$mail = $phrase[2][1];
			$sujet = $phrase[2][2];

			if( !empty($do_str['rappel_facture']) )
			{
				$rf = $do_str['rappel_facture']; 
				exereq($str_maj, [$rappel, $str->acc_id() ]); 
			}
			else
			{
				$dos = structure_facture_dossier(); 
				$dof = genere_facture_pdf($str->acc_id(), $dos); 
				$rf = str_replace('../', '', $dof['chenr']); 
				exereq($str_maj_fact, [$rappel, $rf, $str->acc_id()] ); 
			}
		}
		else
		{
			$etape = "Relance + facture + annonce désactivation de saisis dans deux mois. : déjà éfféctué"; 
		}
	}
	elseif( entre($jour, 61, 90) )
	{
		if( $do_str['rappel'] != 4 )
		{
			$etape = "Relance + facture + annonce désactivation de saisis dans un mois."; 
			$rappel = 4; 
			$envoie_mail = TRUE; 

			$mail = $phrase[2][1];
			$sujet = $phrase[2][2];

			if( !empty($do_str['rappel_facture']) )
			{
				$rf = $do_str['rappel_facture']; 
				exereq($str_maj, [$rappel, $str->acc_id() ]); 
			}
			else
			{
				$dos = structure_facture_dossier(); 
				$dof = genere_facture_pdf($str->acc_id(), $dos); 
				$rf = str_replace('../', '', $dof['chenr']); 
				exereq($str_maj_fact, [$rappel, $rf, $str->acc_id()] ); 
			}
		}
		else
		{
			$etape = "Relance + facture + annonce désactivation de saisis dans un mois. : déjà éfféctué "; 
		}
	}
	elseif( entre($jour, 91, 120) )
	{
		if( $do_str['rappel'] != 5 )
		{
			// Annonce désactivation, mettre la structure en attente. 
			$etape = "Annonce désactivation, mettre la structure en attente."; 
			$rappel = 5; 
			$envoie_mail = TRUE; 

			$mail = $phrase[3][1];
			$sujet = $phrase[3][2];

			exereq($str_maj_des, [$rappel, $str->acc_id() ]); 
		}
		else
		{
			$etape = "Annonce désactivation, mettre la structure en attente. : déjà éfféctué"; 
		}
	}

	exereq($pre_gerant, ['str'=>$str->acc_id() ] ); 

	$tab_gerant = []; 
	$mail_str = FALSE; 

	while($u = fetch($pre_gerant) )
	{
		$tab_gerant[] = $u; 

		if( $envoie_mail && $u['rappel'] )
		{
			$c = new courriel(); 

			if( !empty($u['mail']) )
			{
				$dest = $u['mail'];  
			}
			elseif( !empty($str->acc_mail() ) && !$mail_str )
			{
				$dest = $str->acc_mail(); 
				$mail_str = TRUE; 
			}
			else
			{
				continue; 
			}

			$c->exp = 'adhesion@info-limousin.com'; 
			// $c->dest = 'strategeyti@gmail.com'; 
			$c->dest = $dest; 

			// $c->html = '<p>mail : '.$dest.'</p>'."\n".str_replace(['{USER}'], [$u['User']], $mail); 
			$c->html = str_replace(['{USER}'], [$u['User']], $mail); 

			$c->sujet = $sujet; 

			if( !empty($rf) )
			{
				$c->piece_jointe = RETOUR.$rf; 
			}

			$c->envoie(); 
		}
	}
	
?>
	<tr>
		<td><?php $str->aff_id() ?></td>
		<td><?php $str->aff_nom() ?></td>
		<td><?php $str->aff_actif() ?></td>
		<td><?php $str->aff_date_fin_adhesion() ?></td>
		<td><?php echo $jour ?></td>
		<td><?php echo $etape ?></td>
		<td>
		<?php if(!empty($tab_gerant) ) : ?>
			<?php foreach($tab_gerant as $u ) : ?>
				<?php echo $u['User'] ?>, <?php echo $u['mail'] ?>,<?php echo $str->acc_mail() ?>, 
				<?php if($u['rappel']) : ?>	
					rappel 	
				<?php else : ?>
					Pas de rappel 
				<?php endif ?>
				<br />

			<?php endforeach ?>
		<?php else : ?>
			Aucun gérant, aucun rappel
		<?php endif ?>
		</td>
		<td>
		Sujet : <strong><?php echo $sujet ?></strong><br />
		<br />
		<?php echo $mail ?>
		</td>
		<td>
		<?php var_dump($dof) ?>
		</td>
	</tr>
<?php endwhile ?>
</table>
</body>
</html>
<?php

$var = ob_get_contents(); 
ob_end_clean(); 

if( $verbeu )
{
	echo $var; 
}
else
{
	echo 'success'; 
}


