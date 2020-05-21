<?php
session_start();
unset($_SESSION['loggedin']);
//session_destroy();
// Redirect to the login page:
header('Location: ../index.php');
?>