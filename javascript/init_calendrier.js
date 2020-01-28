
/*
	La variable init_date doit être fourni avant l'appelle à ce script
*/

function disallowDate(date, y, m, d) 
{
	
	var ma = new Date();	
	var ta = new Date( annee, moi, jr );
	
	//alert(ta.toDateString());
	
	if (ma.toDateString() == date.toDateString() || ( date >= ma && date < ta )){
		return false;
	}

	return true;
		
};
	
Calendar.setup({
        inputField:"DateDeb",     // id of the input field
        ifFormat:"%d/%m/%Y",      // format of the input field
        button:"DateDeb_trigger",  // trigger for the calendar (button ID)
        align:"BL",           // alignment (defaults to "Bl")
        singleClick:true,
	firstDay:1,
	dateStatusFunc:disallowDate
});
	


