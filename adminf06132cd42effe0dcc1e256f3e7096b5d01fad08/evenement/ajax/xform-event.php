<?php
require '../../../include/init.php'; 
require C_INC.'evenement_fonc.php'; 

http_param(['id'=>0, 'name' => '', 'value' => '']); 

# 0 : Ok
# 1 : pas le bon champs fournis pour name
# 2 : droit insuffisant 

if( !event_membre_droit($id, $MEMBRE) )
{
	exit('2'); 
}

if( !in_array($name, ['titre', 'description', 'symbole']) )
{
	exit('1'); 
}

$mess = 'Modification rapide depuis la liste des événements.'; 

if( in_array($name, ['titre', 'description']) )
{
	req('UPDATE Evenement_details SET '.$name.'=\''.secubdd($value).'\' WHERE Evenement_id='.(int)$id.' ');

	$champ = $name == 'titre' ? ' du titre ' : ' de la description '; 
	$mess = 'Modification rapide '.$champ.' depuis la liste des événements'; 
}
elseif( $name == 'symbole')
{
	req('UPDATE Evenement SET Cat_id='.(int)$value.' WHERE id='.(int)$id.' ');
	$mess = 'Modification du thème depuis la liste des événements'; 
}

$donne = req('SELECT Actif FROM Evenement WHERE id='.(int)$id.' LIMIT 1 ');
$do = fetch($donne); 

#historique 
$idh = event_insert_commentaire(new evenement(['id'=>$id, 'etat' => $do['Actif'] ]), $MEMBRE->id, $mess, historique::MODIF); 
req('UPDATE Evenement SET der_historique='.(int)$idh.' WHERE id='.(int)$id.' LIMIT 1 ');

echo 0; 
