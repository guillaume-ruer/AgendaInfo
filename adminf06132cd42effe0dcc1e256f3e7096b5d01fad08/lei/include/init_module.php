<?php
non_autorise(GERER_LEI);

if(isset($_GET['s']) )
{
	$_SESSION['import_source'] = $_GET['s']; 
}

$SOURCE = $_SESSION['import_source'] ?? evenement::LEI; 

