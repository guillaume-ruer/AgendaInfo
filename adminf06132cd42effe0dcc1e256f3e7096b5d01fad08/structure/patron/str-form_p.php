<h1>Ma Structure</h1>

<?php pmess() ?>

<?php if( !$traitement ) : ?>

<?php $PAT->affiche_mess() ?>

<form action="str-form.php" method="post" enctype="multipart/form-data" >

<p>Appliquez toutes les modifications nécessaires, puis appuyez sur "ok".</p>

<?php $form->affiche() ?>

<p><input type="hidden" name="p" value="<?php echo $p ?>" />
<input type="submit" name="ok" value="Ok !" /></p>
</form>
<?php endif ?>
