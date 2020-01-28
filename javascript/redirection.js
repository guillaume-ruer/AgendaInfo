$(function(){

	$('body').prepend('<div id="masque" ><div id="masque-message" ><h1>Info-limousin.com</h1> <p>devient</p> <h1><a href="http://agenda-dynamique.com" >agenda-dynamique.com</a></h1><p>Vous allez être redirigé dans quelques secondes</p></div></div>');

	setTimeout(function(){
		window.location.replace('http://agenda-dynamique.com'); 
	}, 10000); 

});
