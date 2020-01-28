<?php
require '../../../include/init.php'; 
require C_INC.'evenement_fonc.php'; 

http_param(['id'=>0, 'etat' => '']); 

# 0 : Ok
# 1 : pas le bon champs fournis pour name
# 2 : droit insuffisant 

if( !event_membre_droit($id, $MEMBRE) )
{
	exit('2'); 
}


if(isset(evenement::$TAB_ETAT[$etat] ) )
{
	switch( $etat)
	{
		case evenement::MASQUE:
			event_maj_etat($id, evenement::MASQUE, $MEMBRE->id, 'Désactivation rapide depuis la liste des événements' );  
		break;
		case evenement::ACTIF : 
			if( droit(MODIF_ETAT) )
			{
				event_maj_etat($id, evenement::ACTIF, $MEMBRE->id, 'Activation rapide depuis la liste des événements' );  
			}
			else
			{
				exit('2'); 
			}
		break; 
		case evenement::SUPP : 
			event_maj_etat($id, evenement::SUPP, $MEMBRE->id, 'Suppression rapide depuis la liste des événements' );  
		break; 
	}
	
	exit('0'); 
}
else
{
	exit('1'); 
}

