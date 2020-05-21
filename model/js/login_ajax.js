$(function() {
$('#login').click(function(e){
    event.preventDefault();

        // get values from FORM
        var username = $("input#username").val();
        var password = $("input#password").val();
        if ( $("input#ckb1").prop( "checked" ) ){
            var rememberme = 1;
        } else {
            var rememberme = 0;
        }
        
        //var rememberme = $("input#remember-me").val();

        $.ajax({
            //when ready to use this, include "model/" within the url area
            url: "../model/authenticate.php",
            type: "POST",
            data: {
                username: username,
                password: password,
                rememberme: rememberme
            },
            //cache: false,
            //async: false,
            success: function(msg) {
                
                if(msg == "success"){
                    location.replace('https://mtdesigns.netau.net/test/dashboard/indexcontent.php?page_name=landing_page.php')
                    //alert("after the relocation");
                }
                else{
                    // Fail message
                    $('#errmsg').html("<div class='alert alert-danger'>");
                    $('#errmsg > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");
                    $('#errmsg > .alert-danger').append("<strong>Error!</strong> " + msg + ". ");
                    $('#errmsg > .alert-danger').append('</div>');
                    //clear all fields
                    $('#password').val('');
                    $('#password').focus();
                }
            },
            error: function(msg) {
                // Fail message
                $('#errmsg').html("<div class='alert alert-danger'>");
                $('#errmsg > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                    .append("</button>");
                $('#errmsg > .alert-danger').append("<strong>Error!</strong> " + msg + ". ");
                $('#errmsg > .alert-danger').append('</div>');
                //clear all fields
                $('#password').val('');
                $('#password').focus();
            }
        })
    })
});
