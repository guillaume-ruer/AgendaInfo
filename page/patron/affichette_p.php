<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe" >
	<div id="bouton_retour" >
		<a href="../" >Agenda</a> - Les affichettes en diffusion
	</div>
</div>

<?php if($event) : ?>
<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe">
	<div id="une_affichette" >
		<?php affichette_aff($event) ?> 
	</div>
</div>

<?php endif ?>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe" >
<?php while($a = $affichette->parcours() ) : $i++; ?>
	<?php if($i > 5 ) : $i=1; ?>
		<div style="clear:both" ></div>
	<?php endif ?>

	<div class="les_affichettes" style="float:left;" >
		<?php affichette_aff($a) ?>
	</div>
<?php endwhile ?>

</div>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>
<div class="fixe" >
	<?php $affichette->acc_pagin()->affiche() ?>
</div>

<div class="fond_souligne" ></div>

