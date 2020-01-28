
function deroulant_defaut(id)
{
	document.getElementById(id).options[0].selected=true;
}

function deux_deroulant_defaut(id1, id2)
{
	deroulant_defaut(id1);
	deroulant_defaut(id2);
}
