<?php
/*
	Par StrateGeyti
	Crée le 12/11/2011 
*/

require '../../include/init.php';
require C_INC.'ls_trace_class.php'; 
require C_INC.'reqa_class.php'; 

/*
	Traitements
*/

http_param( array( 'type' => T_CO, 'p' => 0 ) ); 

$lstrace = new ls_trace('trace', $TRACE_CONF ); 
$lstrace->mut_type( $type ); 
$lstrace->mut_pagin_url( 'trace.php?p=%p&amp;type='.$type ); 
$lstrace->mut_page($p); 
$lstrace = $lstrace->requete(); 
$pagin = $lstrace->pagin; 


/*
	Affichage
*/

require HAUT_ADMIN;
require 'patron/trace_p.php';
require BAS_ADMIN;
