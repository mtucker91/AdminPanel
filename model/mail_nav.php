<?php
session_start();
if(!isset($_SESSION['loggedin'])) {
    $result = "false";
}
else {
    $result = "true";
    
}
die($result);

?>