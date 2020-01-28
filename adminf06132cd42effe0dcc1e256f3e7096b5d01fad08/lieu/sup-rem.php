<?php
require '../../include/init.php'; 

req('DELETE FROM remarquable WHERE id='.(int)$_GET['sup']); 

$res['state'] = 'success'; 

header('Content-type: application/json');  

echo JSON_encode($res); 
