$(function() {
    //gets implemented after the actions of the other scripts
    $('a#Mailbox').click(function(e){
        event.preventDefault();
        $.ajax({
            //when ready to use this, include "model/" within the url area
            url: "model/mail_nav.php",
            type: "GET",
            data: {
                page_name: 'mailbox.php'
            },
            //cache: false,
            //async: false,
            success: function(msg) {
                if(msg == "true"){
                    //$(document.body).load("indexcontent.php?page_name=mailbox.php");
                    //pathway from indexcontent.php location
                    $('.content-wrapper').load("pages/mailbox/mailbox.php?reload=false");
                    window.history.pushState("object or string", "Title", "/test/dashboard/indexcontent.php?page_name=mailbox.php&reload=false");
                }
                else if(msg == "false"){
                 //do nothing
                }
                else {
                    console.log(msg);
                }
        }})
    })
});