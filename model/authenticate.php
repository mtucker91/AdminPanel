<?php
require('connect.php');
session_start();

$username = $_POST['username'];
$password = $_POST['password'];
$rememberme = $_POST['rememberme'];

if(isset($username, $password)){ //if there is something in the text fields
//echo("entered the if statement");
	$db = Cnnct();
	//execute the SQL query and return records
	$sth = $db->prepare("SELECT * FROM ROST_EMPLOYEES WHERE username = ? LIMIT 1");
	$sth->execute([$username]);
	//OR
	//$sth->execute(['username' => $username);
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	//fetch tha data from the database for at least one row.
	foreach($r as $row){
		//************* SIGN IN SUCCESSFUL ***********************************
		if($row['password'] == $password){ //if the password given by the user matches the password in the database.
			//session_regenerate_id();
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['username'] = $row['username'];
			$_SESSION['userid'] = $row['ID'];
			//unset($_SESSION['error']);
			//header('Location: ../home.php');
			$result = "success";
			die($result);
			//die("home.php");
			//go to next page
		}
		//*************** END SIGN IN SUCCESSFUL *********************************
		else{ //else, if the password given by the user DOES NOT match the password in the database.
			$result = "Incorrect Password";
			die($result);
			//header('Location: ../index.php');
			//$error['password'] = "Your password is incorrect.  Please try again."; //display that the password DOES NOT match.
			//return back to sign-in with error
		}
	} //the actual query to be implemented.
	//echo ("Out of Loop");
	if(!isset($result)){
		die("Bad Username");
	}
	
	//header('Location: ../index.php');
}

//echo("Did not notice the POST at all");

/*
From Tutorial
if ($stmt->num_rows > 0) {
	$stmt->bind_result($id, $password);
	$stmt->fetch();
	// Account exists, now we verify the password.
	// Note: remember to use password_hash in your registration file to store the hashed passwords.
	if (password_verify($_POST['password'], $password)) {
		// Verification success! User has loggedin!
		// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;
		echo 'Welcome ' . $_SESSION['name'] . '!';
	} else {
		echo 'Incorrect password!';
	}
} else {
	echo 'Incorrect username!';
}
*/
$db = null; //closing connection created by connect.php
?>