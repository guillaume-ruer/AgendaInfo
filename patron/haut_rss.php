<?php echo '<?php echo \'<?xml version="1.0" encoding="UTF-8" ?>\' ?>' ?>

<rss version="2.0">
	<channel>
		<title><?php echo $PAT->affiche_titre() ?></title>
		<description><?php echo $PAT->val('description') ?></description>
		<link><?php echo $PAT->val('baseurl') ?></link>
		<language><?php echo $PAT->val('lng') ?></language>
		<copyright>Copyright&#169;; 2005 Association Info Limousin.</copyright>
		<webMaster>contact@info-limousin.com</webMaster>
