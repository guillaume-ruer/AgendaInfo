<h1>Statistique de toute l'année <?php echo $annee ?></h1>

<form action="stat.php" method="post" >

	<p>Année : <select name="annee" >
		<?php for($i = 2005; $i <= date('Y'); $i++ ) : ?>
			<option value="<?php echo $i ?>" <?php echo $annee == $i ? 'selected="selected"' : '' ?> ><?php echo $i ?></option>
		<?php endfor ?>
	</select>

	<input type="submit" name="ok" value="Ok !" /></p>

</form>

<ul>
	<li><a href="#lei" >LEI</a></p>
	<li><a href="#departement" >Departement</a></p>
	<li><a href="#contact" >Contact</a></p>
	<li><a href="#categorie" >Catégorie</a></p>
	<li><a href="#theme" >Thème</a></p>
	<?php if( $annee >= 2011) :?>
	<li><a href="#moderateur" >Modérateur</a></p>
	<?php endif ?>
</ul>

<div class="table_defaut" >
<?php echo $xsl->transformToXML($archive) ?>
</div>
