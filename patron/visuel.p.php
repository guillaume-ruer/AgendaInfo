<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe" >
	<div id="bouton_retour" >
		<a href="../" >Agenda</a> - Les <?php echo $nom ?> en diffusion
	</div>
</div>

<?php if($laffichette ) : ?>
	<!-- Ligne fuyante --> <div class="fond_souligne"></div>

	<div class="fixe">
		<div id="une_affichette" >
			<a href="<?php echo secuhtml($v->url) ?>" ><img src="<?php echo C_IMG.'bandeaux/'.secuhtml($v->img) ?>" alt="<?php echo $nom ?>" /></a>
			<?php echo secuhtml($v->texte) ?>
		</div>
	</div>

<?php endif ?>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe" >
<?php while($a = $affichette->parcours() ) : $i++; ?>
	<?php if($i > 4 ) : $i=1; ?>
		<div style="clear:both" ></div>
	<?php endif ?>

	<div class="les_affichettes" style="float:left;" >
	<a href="<?php echo $a->url ?>" ><img src="<?php echo C_IMG.'bandeaux/'.$a->img; ?>" alt="Affichette" /></a><br />
	<div class="texte_affichette" ><?php echo $a->texte; ?></div>
	</div>
<?php endwhile ?>

</div>

<?php if( $affichette->nb_page > 1 ) : ?>
<!-- Ligne fuyante --> <div class="fond_souligne"></div>
<div class="fixe" >
	<div class="pagination" ><span class="libele" >Pages : </span>
		<?php for( $i=0; $i<$affichette->nb_page ; $i++ ) : ?>
		<a href="<?php echo $page.'-'.$i ?>.html" class="<?php echo $i==$p ? 'actif' : 'inactif' ?>" ><?php echo $i+1 ?></a>
		<?php endfor ?>
	</div>
</div>
<?php endif ?>

<div class="fond_souligne" ></div>

