<?php if( MODE_DEV ) : ?>

<?php
$tps = round( (microtime(TRUE)-TPS)*1000, 2); 
$c = (int)(255 * $tps / 1000 );  
$c = $c > 255 ? 255 : $c; 
$color = sprintf("%02X%02X00", $c, 255 - $c ); 
?>

<div id="debug" >
	<span id="debug_plus" <?php if($ERREUR_PHP) : ?>style="color:red"<?php endif ?> >+</span>
	<div>
		<div id="debug_stat" >
		<span style="color:#<?php echo $color ?>" title="Temps de génération de la page en micro-seconde" ><?php echo $tps ?>~</span>
		| <span title="Requêtes" ><?php echo $NB_REQ ?>r</span>
		| <span title="Requêtes préparées" ><?php echo $NB_PRE ?>p</span>
		| <span title="Requêtes préparées executées" ><?php echo $NB_EXE ?>e</span>
		</div>


		<div class="debug_onglet" >
			<div class="debug_titre" >Debug</div>
			
			<div class="debug_feuille" >
			<?php foreach( $TAB_LOG as $err ) : ?>
				<div class="debug_err" ><?php echo $err ?></div>
			<?php endforeach ?>
			</div>
		</div>
		
		<div class="debug_onglet" >
			<div class="debug_titre" >HTTP</div>

			<div class="debug_feuille" >
				<h3>$_POST</h3>
				<div class="debug_err" ><?php var_dump($_POST) ?></div>
				<h3>$_GET</h3>
				<div class="debug_err" ><?php var_dump($_GET) ?></div>
				<?php if( isset($_FILES) ) : ?>
				<h3>$_FILES</h3>
				<div class="debug_err" ><?php var_dump($_FILES) ?></div>
				<?php endif ?>
				<?php if( isset($_COOKIE) ) : ?>
				<h3>$_COOKIE</h3>
				<div class="debug_err" ><?php var_dump($_COOKIE) ?></div>
				<?php endif ?>

			</div>
		</div>

		<div class="debug_onglet" >
			<div class="debug_titre" >SQL</div>
			<div class="debug_feuille" >
				<table>
				<?php foreach($TAB_REQUETE as $REQUETE ) : ?>
					<tr <?php if( $REQUETE['err'] ) : ?>class="debug_sql_err"<?php endif ?> >
						<td><?php echo $REQUETE['type'] ?> <?php echo $REQUETE['tps'] ?></td>
						<td class="debug_sql" ><?php ps($REQUETE['req']) ?></td>
					</tr>
				<?php endforeach ?>
				</table>
			</div>
		</div>

		<div class="debug_onglet" >
			<div class="debug_titre" >SECTION</div>
			<div class="debug_feuille" >
				<?php foreach($PATRON as $num => $SECTION ) : ?>
					<h3><?php echo $SECTION['nom'] ?></h3>
					<div>
						<?php ps('<div class="'.$SECTION['opt']['class'].'" >') ?><br />
						<?php foreach($SECTION['fichier'] as $sec ) : ?>
							&nbsp;&nbsp;&nbsp;&nbsp;<?php ps('<div>') ?><br />
							<?php if( !empty($sec['fichier']) ) : ?>
								<?php echo str_repeat('&nbsp;', 8).$sec['fichier'] ?><br />
							<?php endif ?>
							<?php if( !empty($sec['pat']) ) : ?>
								<?php echo str_repeat('&nbsp;', 8).$sec['pat'] ?><br />
							<?php endif ?>
							&nbsp;&nbsp;&nbsp;&nbsp;<?php ps('</div>') ?>
							<br />
						<?php endforeach ?>
						<?php ps('</div>') ?>
						<br />
					</div>
				<?php endforeach ?>
			</div>
		</div>
		<div class="debug_onglet" >
			<div class="debug_titre" >URL Rewriting</div>
			<div class="debug_feuille" >
				<?php foreach($URL_TAB_ERREUR as $URL_ERREUR) : ?>
					<?php echo $URL_ERREUR ?>
				<?php endforeach ?>
			</div>
		</div>

		<div class="debug_onglet" >
			<div class="debug_titre" >SERVER</div>
			<div class="debug_feuille" >
				<h3>$_SESSION</h3>
				<div class="debug_err" ><?php var_dump($_SESSION) ?></div>
				<h3>$_SERVER</h3>
				<div class="debug_err" ><?php var_dump($_SERVER) ?></div>
			</div>
		</div>
		<div class="debug_onglet" >
			<div class="debug_titre" >Javscript</div>
			<div class="debug_feuille" >
				<div id="debug_js" ></div>
			</div>
		</div>
	</div>
</div>

<?php endif ?>
