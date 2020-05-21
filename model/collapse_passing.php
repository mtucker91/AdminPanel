<?php
require('connect.php');
session_start();
echo('got session started');
$userid = $_SESSION['userid'];
$collapse = $_POST['collapse'];
echo('got variables');
 //if there is something in the text fields
//echo("entered the if statement");
$db = Cnnct();
//execute the SQL query and return records
$sth = $db->prepare("UPDATE USER_PREF SET Sidebar_Closed = ? where ID = ?");
$sth->execute([$collapse, $userid]);
//OR
//$sth->execute(['username' => $username);
//$r = $sth->fetchAll(PDO::FETCH_ASSOC);
//fetch tha data from the database for at least one row.
//foreach($r as $row){

//} //the actual query to be implemented.
unset($db); //closing connection created by connect.php
?>