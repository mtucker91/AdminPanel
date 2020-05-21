<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: view/login.php');
	exit();
}
//require('model/starting_index.php');

if(isset($_GET['signout'])){
	unset($_SESSION['displaytime']);
	setcookie('username', '', time() - (3601 * 24), "/"); //unset the cookie
	//unset the variable so the "sign-out" option in the header will no longer show.
	unset($_SESSION['signout_avail']); 
	require('view/template/header.php'); //load header
	require('view/single_sign_in.php'); //load sign-in screen
}
else if(isset($_POST['signin'])){
	
	setLogin($ID, $_POST['username'], $_POST['password']);
}
else if(isset($_POST['clocking'])){
	require('view/template/header.php'); //load header
	clocking($ID, $_SESSION['username'], $_POST['PIN']);
	require('view/home.php');
}
else if(isset($_POST['break'])){
	require('view/template/header.php'); //load header
	breaks($ID, $_SESSION['username'], $_POST['PIN']);
	require('view/home.php');
}
else if(isset($_POST['lunch'])){
	require('view/template/header.php'); //load header
	lunch($ID, $_SESSION['username'], $_POST['PIN']);
	require('view/home.php');
}
else if(isset($_COOKIE['username'])){
	require('view/template/header.php'); //load header
	cookieLogin($ID, $_COOKIE['username']);
}
else{
	require('view/template/header.php');
	require('view/single_sign_in.php');
}

?>