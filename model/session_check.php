<?php
if(!isset($_SESSION['loggedin'])) {
    echo('You are not logged into the portal at this time.  Please go back to the login screen.');
}
else if(isset($_GET['page_name'])){
    if($_GET['page_name'] == 'landing_page.php'){
        require('landing_page.php');
    }
    else if($_GET['page_name'] == 'mailbox.php'){
        require('pages/mailbox/mailbox.php');
    }
}
?>