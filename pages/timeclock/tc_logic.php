<?php
session_start();
if(!isset($_SESSION['loggedin'])) {
    $result = "false";
    die($result);
}

require('../../model/connect.php');
//echo("connecting");
require('clocking_func.php');
//echo("getting functions ready");
$ID = getID($_SESSION['username']);
//echo("get ID from session username variable");
if(isset($_POST['time_option'])){
    
    $result = clocking($ID, $_SESSION['username'], $_POST['PIN'], $_POST['time_option']);
    
}

die($result);
?>