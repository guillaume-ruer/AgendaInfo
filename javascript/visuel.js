
var traitement_contact = function (rep)
{
	var select= document.getElementById('md_contact'); 

	while( c = select.firstChild )
		select.removeChild(c); 

	if( rep != null )
	{

		var tab=rep.getElementsByTagName('contact'); 
		
		if( tab.length == 0 )
		{
			select.setAttribute('disabled', 'disabled'); 
		}
		else
		{
			select.removeAttribute('disabled'); 
			var opt, text;
				id = 0; 
				nom = "Sans contact"; 
				opt = document.createElement('option'); 
				text = document.createTextNode(nom); 
				opt.setAttribute('value', id); 
				opt.appendChild(text); 
				select.appendChild(opt); 

			for( i=0; i<tab.length; i++ )
			{
				id = tab[i].getAttribute('id'); 
				nom = tab[i].getAttribute('donne'); 
				opt = document.createElement('option'); 
				text = document.createTextNode(nom); 
				opt.setAttribute('value', id); 
				opt.appendChild(text); 
				select.appendChild(opt); 
			}

		}
	}
	else
	{
		select.setAttribute('diabled', 'disabled'); 
		alert('pouette'); 
	}

}

function maj_contact()
{
	var url = 'maj_contact.php?ids='+document.getElementById('ids').value; 
	req( traitement_contact, url ); 	
}


