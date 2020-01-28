<?php
require '../../include/init.php'; 

$lsr = new remarquable_ls(genere_init([
	'mode' => reqo::PAGIN, 
	'pagin__num_page' => isset($_GET['p']) ? (int)$_GET['p'] : 0, 
	'pagin__url' => 'remarquable.php?p=%pg'
])); 
$lsr->requete(); 

require PATRON; 
