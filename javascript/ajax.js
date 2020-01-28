function creexhr()
{
	var xhr = null;
		
	if (window.XMLHttpRequest || window.ActiveXObject) 
	{
		if (window.ActiveXObject) 
		{
			try 
			{
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} 
			catch(e) 
			{
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} 
		else 
		{
			xhr = new XMLHttpRequest(); 
		}
	} 
	else 
	{
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return false;
	}
	
	return xhr; 
}



function req(fonction, url)
{
	var xhr = creexhr(); 

	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			fonction(xhr.responseXML); 

		}
	};

	xhr.open("GET", url, true);
	xhr.send(null);
}

var traitement = function(rep)
{
	var reception = document.getElementById('info_contact'); 
	supp_enfant(reception); 

	var nom = contenu(rep, 'nom');
	if( nom == '' )
	{
		nom = "non diffusé dans l'agenda"; 
	}
	var site = contenu(rep, 'site');
	if( site == '' )
	{
		site = "non diffusé dans l'agenda" ;
	}
	var mail = contenu(rep,'mail');
	if(mail == '' )
	{
		mail = "non diffusé dans l'agenda"; 
	}
	var tel = contenu(rep, 'tel');
	if( tel == '' )
	{
		tel = "non diffusé dans l'agenda"; 
	}

	var t = document.createTextNode('Info : '+nom+', '+tel+', ['+site+']'); 
	reception.appendChild(t);

	var br = document.createElement('br'); 
	reception.appendChild(br); 

	t = document.createTextNode('Email : ' +mail); 
	reception.appendChild(t); 
}

var traitement_img_theme = function (rep )
{
	if(rep)
	{
		var img = contenu(rep, 'image'); 
		var width = contenu(rep, 'width'); 
		var height = contenu(rep, 'height'); 
		
		gebi('imgsym').removeAttribute('width'); 
		gebi('imgsym').removeAttribute('height'); 

		if( width)
		{
			gebi('imgsym').width = width; 
		}

		if( height )
		{
			gebi('imgsym').height = height; 
		}

		gebi('imgsym').src='http://info-limousin.com/img/symboles/'+img; 
	}
}

function contenu(doc, nom)
{
	var cont=''; 
	if(doc )
	{
		if( bal = doc.getElementsByTagName(nom)[0].firstChild)
		{
			cont = bal.nodeValue; 
		}
	}
	return cont; 
}
