/*
	Basique 
*/

function sup(id)
{
	var node = gebi(id); 
	var tmp; 
	while( tmp = node.firstChild )
	{
		node.removeChild(tmp); 
	}
}

/*
	Spécifique 
*/
function nbcar()
{
	sup("nbcar"); 
	sup("app"); 
	var nbc = gebi("description").value.length;

	if( nbc < 20 )
	{
		chaine="trop court";
	}
	else if (nbc < 150 )
	{
		chaine="optimisé"; 
	}
	else if (nbc <300 )
	{
		chaine="développé";
	}
	else 
	{
		chaine="un peu long"; 
	}

	gebi("nbcar").appendChild(dctn(nbc) );
	gebi("app").appendChild(dctn(chaine) );

}

function selmin(id)
{
	var node=gebi(id); 
	var text=node.value;
	var deb=text.substring(0, node.selectionStart); 
	var fin=text.substring(node.selectionEnd); 
	var sel=text.substring(node.selectionStart, node.selectionEnd);
	node.value=deb+sel.toLowerCase()+fin; 
}

function homogene()
{
	var node=gebi("description"); 
	var text=node.value; 
	var scroll = node.srollTop; 

	/*
		Il se peut qu'il y ai des url. 
		Avant de commencer à modifier le texte, on va les transformer en balise de type {{addr[num]}}. 
		Ensuite modifier le texte. 
		Et enfin remplacer les balise par les ancienne url. 
		De cette façon on évite de modifier les url par mégarde. (qui du coup ne pointerais plus vers une page existante... )

		Remplacement des url | mail 
	*/
	reg = new RegExp("((((https?://)|(www\.))[^\t\n ]+))|([a-zA-Z_.-]+@[a-z-]+\.[a-z]{1,4})" ); 
	
	var cherche=reg.exec(text);
	var i=0;
	var tabaddr=Array(); 
	while(cherche != null )
	{
		tabaddr[i]=cherche[0]; 
		text=text.replace( reg, '{{addr'+i+'}}');
		cherche=reg.exec(text);
		i++;
	}

	/*
		Ici on fait tout nos traitements... 
	*/

	// Heur
	text=text.replace(/(\d{1,2})\s?heure?s?(\s?\d{1,2})?/ig, '$1h$2'); 
	text=text.replace(/(\d{1,2})\s?h(\s(\d{1,2}[^h€]))?/ig, '$1h$3'); 
	text=text.replace(/(?:de\s?)?(\d{1,2})\s?h(\s?\d{1,2})?\s?[à-]\s?(\d{1,2})\s?h(\s?\d{1,2})?/ig, ' $1h$2 à $3h$4'); 
	text=text.replace(/(\d{1,2})h00/ig, '$1h'); 
	text=text.replace(/(\d+)\s?(mn|minutes?)/ig, '$1min'); 

	// Tèl 
	text=text.replace(/(\d{2})[. ,-]?(\d{2})[. ,-]?(\d{2})[. ,-]?(\d{2})[. ,-]?(\d{2})/g, '$1 $2 $3 $4 $5'); 

	// Espace ponctuation
	text=text.replace(/\.\s*\./g, '.'); // Transforme un double point en un seul. 
	text=text.replace(/\s*(\.{1,3}|,) */g, '$1 '); 
	text=text.replace(/\s*([;!:?])\s*/g, ' $1 '); 
	text=text.replace(/(\()\s*/g, '$1'); // Enlève l'espace après une ouverture de parenthèse. 
	text=text.replace(/(\s*\|\s*)|(\s+-\s*)|(\s*-\s+)/g, ', '); // Transforme les séparation en virgule.  


	// km, euro
	text=text.replace(/(\d+)\s*(kilomètres?|kms?)/ig, '$1km'); 
	text=text.replace(/([0-9]+)\s*euros/ig, '$1€'); 
	text=text.replace(/([0-9]+)\s*euro([^\w]|$)/ig, '$1€$2'); 
	text=text.replace(/(\d+)\s*€/ig, '$1€'); 
	text=text.replace(/(\d+)\s*[,.]\s*(\d+)\s?(€|km)/ig, '$1.$2$3'); 

	/*
		Retransforme les url 
	*/
	var j=0; 
	for(j=0; j<i; j++ )
	{
		reg.compile("{{addr"+j+"}}");
		text=text.replace(reg, tabaddr[j]); 
	}

	// Espace 
	text=text.replace(/ {2,}/g, ' '); 
	text=trim(text);
	
	// Ponctuation final
	if( text.match(/[^.!?]$/) ) 
	{
		text+='.';
	}

	// Majuscule au début 
	text = text.charAt(0).toUpperCase() + text.substring(1); 	
	node.value=text; 
	
	node.focus(); 	
	node.scrollTop = scroll; 
}

function trim (myString)
{
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
} 
