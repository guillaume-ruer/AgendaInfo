function insert_phrase(phrase, textareaId ) {
        var field  = document.getElementById(textareaId); // On récupère la zone de texte
        var scroll = field.scrollTop;                     // On met en mémoire la position du scroll
        field.focus(); // On remet le focus sur la zone de texte, suivant les navigateurs, on perd le focus en appelant la fonction. 
        
        // Reste du code ici ^^
        
   if (window.ActiveXObject) { // C'est IE
                var textRange = document.selection.createRange();            
                var currentSelection = textRange.text;
                
                textRange.text = phrase + currentSelection;
                textRange.moveStart("character", - currentSelection.length);
                textRange.select();     
        } else { // Ce n'est pas IE
                var startSelection   = field.value.substring(0, field.selectionStart);
                var currentSelection = field.value.substring(field.selectionStart, field.selectionEnd);
                var endSelection     = field.value.substring(field.selectionEnd);
                
                field.value = startSelection + phrase +currentSelection + endSelection;
                field.focus();
                field.setSelectionRange(startSelection.length, startSelection.length + currentSelection.length);
        } 

        field.scrollTop = scroll; // et on redéfinit le scroll.
}
