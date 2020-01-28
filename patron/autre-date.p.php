
<!-- Ligne fuyante --> <div class="fond_souligne"></div>


<div class="fixe" >
	<div id="bouton_retour" >
		<a href="<?php echo RETOUR ?>" >Agenda</a> - <a href="<?php echo ancien_url() ?>" >Retour</a> - Autres dates
	</div>
</div>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe texte_gros" >
    <div id="bl_event" >
        <h3 id="texte_titre" ><?php $ev->aff_titre() ?></h3>
        <?php if( $ev->acc_image() ) : ?>
        <div style="padding:5px; float:left" >
        <img src="<?php echo C_EVENT_IMAGE.$ev->acc_image() ?>" alt="Image de l'évenement" />
        </div>
        <?php endif ?>
        <p id="texte_description" >
        <?php $v=''; foreach($ev->acc_tab_lieu() as $lieu ) : 
        ps( $v.$lieu->acc_nom() ); 
        $v=', '; endforeach ?>.

        <?php $ev->aff_desc() ?>

        </p>

        <p class="event_contact" ><span class="texte_contact" >
            <?php ps( $ev->acc_contact()->acc_structure()->acc_nom() ) ?>
            <?php ps( $ev->acc_contact()->acc_titre() ) ?>
            <?php $ev->aff_source() ?>	
                <?php ps( $ev->acc_contact()->acc_tel() ) ?>
                <?php if( $ev->acc_contact()->acc_site() != '' ) : ?>	
                    [<a href="<?php $ev->acc_contact()->aff_site() ?>" ><?php $ev->acc_contact()->aff_site() ?></a>]
                <?php endif ?>

                </span>
            <a href="<?php echo $ev->acc_contact()->acc_url(); ?>" >
            <img src="<?php echo C_BOUTON; ?>infolimo-info-bj.jpg" alt="info-structure" 
                title="Plus d'infos sur la structure qui a mis en diffusion cette info" 
                onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-infob-bj.jpg';" 
                onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-info-bj.jpg'; " /></a> 
            <a href="http://www.facebook.com/share.php?u=info-limousin.com/page/autre-date.php?id=<?php echo $ev->acc_id() ?>" >
            <img src="<?php echo C_BOUTON; ?>infolimo-facebook-bj.jpg" alt="Diffuser-Facebook" 
                title="Diffusez cette info sur votre compte Facebook" 
                onmousemove="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-facebookb-bj.jpg';" 
                onmouseout="javascript:this.src='<?php echo C_BOUTON; ?>infolimo-facebook-bj.jpg'; " /></a>
        </p>
    </div>
</div>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>

<div class="fixe texte_gros" >
	<h3>Dates</h3>

<?php if( !$ev->acc_tab_date() )  : ?>

	<p>Cette événement n'a plus de dates.</p>

<?php else : ?>
    <p><?php echo date('Y') ?><?php echo date('n')+NB_MOIS-1 > 12 ? '/'.(date('Y')+1 ) : '' ?>. 
    Les dates de l'&eacute;venement sont surlign&eacute;es en bleu.
    </p>

	<table>
		<tr>
		<?php for( $j=0; $j<NB_MOIS; $j++ ) : ?>
			<th colspan="2" ><?php echo $tab_date[ (date('n')+$j-1)%12 ] ?></th>
		<?php endfor ?>
		</tr>
	<?php for($i=1; $i<=31; $i++ ) : ?>
		<tr>

		<?php for( $j=0; $j<NB_MOIS; $j++ ) : ?>
			<?php 
			$time =  mktime(0,0,0, (date('n')+$j-1 )%12+1, $i, date('Y') + (date('n')+$j>12?1:0) ); 
			$jr = ''; 
            $nb = ''; 
			$class = ''; 

			if( checkdate( (date('n')+$j-1)%12+1, $i, date('Y', $time) ) )
			{
				$jr = $tab_jour[ date('w', $time ) ]; 
                $nb = $i; 

				if( in_array(date('Y-m-d', $time ), $ev->acc_tab_date() ) )
				{
					$class = 'date_active'; 
				}
			}
			?>
            
            <td class="<?php echo $nb ? 'chiffre' : '' ?>" ><?php echo $nb ?></td> 
			<td class="<?php echo $class ?>" ><?php echo $jr ?></td>
		<?php endfor ?>
		</tr>
	<?php endfor ?>
	</table>


<?php endif ?>
</div>

<!-- Ligne fuyante --> <div class="fond_souligne"></div>
