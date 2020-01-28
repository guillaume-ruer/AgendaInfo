<h1>Mon Compte</h1>

<p><a href="form.php" >Modifier mon compte</a></p>

<p>Adresse mail : <strong><?php echo empty($MEMBRE->mail) ? 'Non renseigné' : $MEMBRE->mail ?></strong>.</p>

<?php if( $MEMBRE->compte_rendu ) : ?>
<p>Vous recevez par mail un compte rendu quotidien.</p>
<?php endif ?>

<?php if( $MEMBRE->notif ) : ?>
<p>Vous recevez par mail des notifications sur l'activité du site (inscription pour le moment).</p>
<?php endif ?>
