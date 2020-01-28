<?php
/*
	Par StrateGeyti
	Crée le 06/11/2011 
*/
require '../../include/init.php';
/*
	Traitements
*/

$tab_page = array(
	'location.php',
	'location-admin.php'
); 

http_param( array( 'p' => 0, 'code' => 0, 'page' => 0, 'ids' => 0 ) ); 

$page = isset($tab_page[ $page ]) ? $tab_page[ $page ] : $tab_page[0]; 
$url = $page.'?p='.$p.'&amp;ids='.$ids;

/*
	Affichage
*/

require HAUT_ADMIN;
?>

<h1>Voir un relais</h1>

<p><a href="<?php echo $url ?>" >Retour</a></p>

<p>
  <script type="text/javascript" >
        <!--
        IL_Lang = "FR";
        IL_Code = "<?php echo $code ?>";
        IL_Hauteur = "600";
        IL_Largeur = "800";
        //-->
</script>
<script src="../../externe/externe.php" type="text/javascript" ></script>
  
</p>

<hr />

<p>Code &agrave; ins&eacute;rer dans une page de votre site, vous contr&ocirc;lez la hauteur et la largeur 
de la fen&ecirc;tre, selon votre template, vous pouvez mettre en pourcentage, nous g&eacute;rons 
le CSS (couleurs)  apr&egrave;s la mise en ligne :  
</p> 

<p>&lt;script type=&quot;text/javascript&quot;&gt;<br />
&lt;!--<br />
  IL_Lang = &quot;FR&quot;;<br />
  IL_Code = &quot;<?php echo $code ?>&quot;;<br />
  IL_Hauteur = &quot;1200&quot;;<br />
  IL_Largeur = &quot;600&quot;;<br />
  //--&gt;<br />
&lt;/script&gt;<br />
&lt;script src=&quot;http://www.info-limousin.com/externe.php&quot; language=&quot;javascript&quot;&gt;&lt;/script&gt;
</p>

<?php require BAS_ADMIN ?>
