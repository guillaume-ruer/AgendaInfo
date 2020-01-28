<?php
require_once '../../include/init.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'abo_option_class.php'; 

if( !droit(GERER_UTILISATEUR) )
{
	page_erreur(); 
}

function model_option($opt=NULL)
{
	if(is_null($opt) )
	{
		$opt = new abo_option; 
	}

	echo '
	<p class="abo-option" >
		Description : <input type="text" size="40" name="description[]" value="'.secuhtml($opt->acc_description() ).'" />
		Prix : <input type="text" size="2" name="prix[]" value="'.(float)$opt->acc_prix().'" />
		<input type="hidden" name="id[]" value="'.$opt->acc_id().'" />
		<input class="option-sup" type="button" name="sup" value="Supprimer" />
	</p>'; 
}

$PAT->ajt_haut('str-menu_p.php', C_ADMIN.'structure/patron/');

http_param(array('ids' => 0, 'p' => 0) ); 
$str = str_init($ids); 



if( isset($_POST['ok']) )
{

	$payant = isset($_POST['payant']); 

	req('UPDATE structure SET payant='.(int)$payant.' WHERE id='.$str->acc_id() ); 
	$str->mut_payant($payant); 

	$majopt = prereq('UPDATE abo_option SET prix=?, description=? WHERE id=? AND structure=? ');
	$tab_option = []; 
	$tab_noption = []; 

	if(isset($_POST['id']) )
	{
		foreach($_POST['id'] as $i => $id )
		{
			if( $id == -1 )
			{
				continue; 
			}

			$opt = new abo_option([
				'id' => $id,
				'description' => $_POST['description'][$i], 
				'prix' => str_replace(',', '.', $_POST['prix'][$i]),
				'structure' => $str,
			]); 

			if( empty($id) )
			{
				// Nouvelle option 
				$tab_noption[] = '(\''.secubdd($opt->acc_description() ).'\',\''.str_replace(',','.', (float)$opt->acc_prix() ).'\','.$opt->acc_structure()->acc_id().')'; 
			}
			else
			{
				$tab_option[] = $opt->acc_id(); 

				exereq($majopt, [
					$opt->acc_prix(), 
					$opt->acc_description(), 
					$opt->acc_id(), 
					$opt->acc_structure()->acc_id() 
				]);
			}
		}
	}

	// Supprime toute les options de la str qui ne sont pas dans le tableau. 
	$cond = empty($tab_option) ? '' : 'AND id NOT IN('.implode(',', $tab_option).')'; 
	req('DELETE FROM abo_option WHERE 1 '.$cond.' AND structure='.(int)$str->acc_id().' ');

	// Ajout des options pas encore présentes. 

	if( !empty($tab_noption) )
	{
		req('INSERT INTO abo_option(description, prix, structure) VALUES'.implode(',',$tab_noption) ); 
	}
}

// Récupération des options

$lso = new reqo([
	'sorti' => 'abo_option' 
]);

$lso->requete('SELECT id, prix, structure structure__id, description FROM abo_option WHERE structure='.(int)$str->acc_id().' ');

require PATRON; 
