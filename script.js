document.onkeyup = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 37) {
        document.forms["formLeft"].parentElement.style.backgroundColor = "#5199fa" ;

        setTimeout(function(){
            document.forms["formLeft"].submit();
        }, 1);

        //avertAdditionalActions();
    }

    if (evt.keyCode == 39) {
        document.forms["formRight"].parentElement.style.backgroundColor = "#5199fa" ;

        setTimeout(function(){
            document.forms["formRight"].submit();
        }, 1);

        //avertAdditionalActions();
    }
};

/*function goBack() {
    document.forms["formRight"].submit();
};


function avertAdditionalActions() {
    //document.getElementById("pictures").remove();
};*/