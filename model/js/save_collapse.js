$(function() {
    //gets implemented after the actions of the other scripts
    $('li#hamburger').click(function(e){
        event.preventDefault();
            // get values from FORM
            if ( $("body").hasClass( "sidebar-collapse" ) ){
                //send the opposing value, because by the time this js
                //activates, it no longer has the sidebar-collapse class.
                var collapse = 0;
            } else {
                var collapse = 1;
            }
            
            //var rememberme = $("input#remember-me").val();
    
            $.ajax({
                //when ready to use this, include "model/" within the url area
                url: "model/collapse_passing.php",
                type: "POST",
                data: {
                    collapse: collapse
                },
                //cache: false,
                //async: false,
                success: function(msg) {
                    //do nothing
            }})
        })
    });