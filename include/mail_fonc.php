<?php

function mel($dest, $sujet, $message, $wrap=TRUE)
{
	$pl = preg_match('`^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$`', $dest ) ? "\n" : "\r\n" ;

	if( $wrap )
		$message = wordwrap($message, 70, $pl, TRUE ); 

	$entete = 'MIME-version: 1.0'.$pl;
	$entete .= 'Content-type: text/plain; charset=utf8'.$pl;
	$entete .= 'From:info-limousin<contact@info-limousin.com>'; 
	mail($dest, $sujet, $message,  $entete ); 
}
