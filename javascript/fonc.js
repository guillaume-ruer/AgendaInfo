function htmlentities(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}

function html_entity_decode(value){
    return String(value)
        .replace(/&quot;/g, '"')
        .replace(/&#39;/g, "'")
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&');
}

function debug(ch)
{
	$('#debug_js').append( $('<p>').append( htmlentities(JSON.stringify(ch) ) ) ); 
}

var images = new Array()

function preload() 
{
	for (i = 0; i < preload.arguments.length; i++) 
	{
		images[i] = new Image();
		images[i].src = preload.arguments[i]; 
		images[i].onload = (function(src){ return function(){
			debug('image '+src+' chargé ! '); 
		}})(preload.arguments[i]);
		debug(preload.arguments[i]); 
	}
}
