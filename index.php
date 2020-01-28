<?php
require 'include/init.php';
require RETOUR. 'type_page/include/init_module.php'; 

$type_page = (isset($_GET['tp']) ) ? (int)$_GET['tp'] : 0 ;
$tab_page = array('normal.php', 'ext.php', 'rss.php', 'rss_ext.php');  
$inc = (isset($tab_page[$type_page]) ) ?  $tab_page[$type_page] : $tab_page[0]; 
require 'type_page/'.$inc; 
