function displayAlert(msg, encoded) {
    if(encoded == true){
        var updmsg = JSON.parse(msg); //already encoded from the php side
    }
    else{
        var updmsg = msg;
    }
    $('#success').html("<div class='alert alert-" + updmsg[1] + "'>");
    $("#success > .alert-" + updmsg[1] + "").html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
        .append("</button>");
    if(updmsg[1] == "success"){
        $("#success > .alert-" + updmsg[1] + "").append("<h5><i class='icon fas fa-check'></i> Success!</h5>");
    }
    else if(updmsg[1] == "danger"){
        $("#success > .alert-" + updmsg[1] + "").append("<h5><i class='icon fas fa-ban'></i> Alert!</h5>");
    }
    else if(updmsg[1] == "warning"){
        $("#success > .alert-" + updmsg[1] + "").append("<h5><i class='icon fas fa-exclamation-triangle'></i> Warning!</h5>");
    }
    $("#success > .alert-" + updmsg[1] + "")
        .append("<strong>" + updmsg[0] + "</strong>");
    $("#success > .alert-success" + updmsg[1] + "")
        .append('</div>');
    $('#PIN').val(""); //clear PIN field
    setTimeout(() => {$('div.alert').fadeOut("slow")}, 3000);


}

function displayStatus(msg, encoded) {
    if(encoded == true){
        var updmsg = JSON.parse(msg); //already encoded from the php side
    }
    else{
        var updmsg = msg;
    }
    $('#stat-placeholder').html(updmsg[2]);
    $('#stat-placeholder').css({"color": updmsg[3]});
    //cannot work on the initial clock-in because the span#time-placeholder does not exist
    if($('#time-placeholder').length){
        /*do nothing but continue with the rest of 
        the function because nothing else is needed*/
    }
    else{
        $('#tc-status-ln2 > h4').html("at <span id='time-placeholder'></span>");
    }

    $('#time-placeholder').css({"color": updmsg[3]});
    $('#time-placeholder').html(updmsg[7]);

}

function changeDisStat(msg, encoded){
    if(encoded == true){
        var updmsg = JSON.parse(msg); //already encoded from the php side
    }
    else{
        var updmsg = msg;
    }
    if(updmsg[4] == true){
        $("option#in-out").attr('disabled','disabled');
    }
    else{
        $("option#in-out").removeAttr('disabled');
    }
    if(updmsg[5] == true){
        $("option#break").attr('disabled','disabled');
    }
    else{
        $("option#break").removeAttr('disabled');
    }
    if(updmsg[6] == true){
        $("option#lunch").attr('disabled','disabled');
    }
    else{
        $("option#lunch").removeAttr('disabled');
    }
}

$(function() {
    //gets implemented after the actions of the other scripts
    $('button#tc-submit').click(function(e){
        event.preventDefault();
        var time_option = $("select#timeclock-dropdown").val();
        if(time_option == ""){
            var msg = ["Please select a Clocking option before the submit button", "warning", "f", "f","","",""];
            displayAlert(msg, false);
            return;
        }
        var PIN = $("input#PIN").val();
        $.ajax({
            //when ready to use this, include "model/" within the url area
            url: "pages/timeclock/tc_logic.php",
            type: "POST",
            data: {
                page_name: 'clockin.php',
                time_option: time_option,
                PIN: PIN
            },
            //cache: false,
            //async: false,
            
            success: function(msg) {
                
                $('.content-wrapper').load("pages/timeclock/clockin.php?reload=false");
                window.history.pushState("object or string", "Title", "/test/dashboard/indexcontent.php?page_name=clockin.php&reload=false");
                setTimeout(() => {displayAlert(msg, true);}, 100);
                //setTimeout(displayAlert(msg, true),3000);
                //$("#btnSubmit").attr("disabled", false);
                //displayAlert(msg, true);
                //displayStatus(msg, true);
                //changeDisStat(msg, true);
            },
            error: function(msg) {
                //displayAlert(msg, true);
            }
    
    })
    })
});