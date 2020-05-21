<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: view/login.php');
	exit();
}
else if(isset($_GET['pagename'])){
	header('Location: view/login.php');
}
else {
	header('Location: indexcontent.php?page_name=landing_page.php');
}

?>