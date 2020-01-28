function supp_enfant(node)
{
	var enfant; 
	while(enfant = node.firstChild )
	{
		node.removeChild(enfant); 
	}
}

function cree_ele(nom, val)
{
	ele = document.createElement(nom); 
	if(val)
	{ 
		t = document.createTextNode(val); 
		ele.appendChild(t); 
	}

	return ele; 
}
function mess(m) 
{ 
	
	if( !(node = document.getElementById('mess') ) )
	{
		node = dce('div'); 
		node.setAttribute('style', 'position:fixed; width:300px; height:300px; right:0px; top:0px; border:3px solid #DDDDDD; background:black; color:white; overflow:auto'); 
		node.setAttribute('id', 'mess'); 
		document.getElementsByTagName('body')[0].appendChild(node); 
	}

	t = document.createTextNode(m);  

	if(child = node.firstChild ) 
	{ 
		br = document.createElement('br');  
		node.insertBefore(br, child);  
		node.insertBefore(t, br);  
	} 
	else 
	{ 
		node.appendChild(t);  
	} 

}
	
function create_input(type, name, value )
{
	input = dce('input');
	input.setAttribute('type', type); 
	input.setAttribute('name', name);
	input.setAttribute('value', value);
	return input; 
}

function dctn(text)
{
	return document.createTextNode(text); 
}

function dce(name)
{
	return document.createElement(name); 
}

function gebi(id)
{
	return document.getElementById(id); 
}

function ajt_event(node, evenement, fonction)
{
	if(document.all) 
	{
		node.attachEvent('on'+evenement,fonction);
	}
	else 
	{
		node.addEventListener(evenement,fonction,true);
	}	
	
	return node; 
}


