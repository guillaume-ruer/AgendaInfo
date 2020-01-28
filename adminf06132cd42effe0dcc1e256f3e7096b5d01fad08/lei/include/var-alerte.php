<?php

define('VERIFIER', 1 );
define('NON_VERIFIER', 0); 

define('ALERTE_LEI_SUPP', 0 );
define('ALERTE_LEI_MODIF', 1 );
define('ALERTE_LEI_DATE', 2 );

$TYPE_ALERTE = array(
	ALERTE_LEI_SUPP => 'Suppression',	
	ALERTE_LEI_MODIF => 'Modification',	
	ALERTE_LEI_DATE => 'Date'

);
