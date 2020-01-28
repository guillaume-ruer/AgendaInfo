<?php
require '../../include/init.php'; 

$lsg = req('SELECT id, nom FROM structure_grp'); 

require PATRON;
