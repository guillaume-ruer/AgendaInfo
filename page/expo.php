<?php
include '../include/init.php'; 

$STYLE = 'style_expo.css'; 
include HAUT_PAGE;
include C_INC.'fonc_bandeau.php'; 
$lien = 'expo.php'; 
$tab_expo = recupe_bandeau('expo', 14, TRUE, $lien ); 
?>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe" >
	<div id="bouton_retour" >
		<a href="../" >Agenda</a> - Les expositions en cours </div>
</div>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>
<div class="fixe" >
<?php foreach($tab_expo as $do ) { extract($do); ?>
	<div class="les_expos" style="float:left;" >
	<a href="<?php echo $url; ?>" ><img src="<?php echo $image; ?>" alt="Affichette" /></a><br />
	<div class="texte_expos" ><?php echo $texte; ?></div>
	</div>
<?php } ?>
</div>

<div class="fixe" >
<?php echo $lien ; ?>
</div>
<div class="fond_souligne" ></div>
<?php
include BAS; 
?>
