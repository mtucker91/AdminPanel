<?php

function Cnnct(){
	$usern = "id1727844_roster";
	$dbn = "id1727844_roster";
	$hostname="localhost";
	$passw="reaper91";
	$db = new PDO("mysql:host=$hostname;dbname=$dbn", $usern, $passw);
	return $db; 
}

function CheckTime($ID){
	$db = Cnnct();
	$sth = $db->prepare("SELECT * FROM ROST_HIST_TIME WHERE ID = '".$ID."' AND DATE(Time_Cur) = DATE(CURDATE()) ORDER BY Time_Cur DESC LIMIT 1");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		if($row['In_Out'] == "i" && !isset($row['Brk_Lnch'])){
			$_SESSION['message'] = "CLOCKED IN";
			$_SESSION['error'] = "";
			$_SESSION['statuscolor'] = "#00e600";
			setUserFriendlyDate($ID);
			return;
		}
		else if($row['In_Out'] == "o" && !isset($row['Brk_Lnch'])){
			$_SESSION['inoutdisabled'] = true;
			$_SESSION['breakdisabled'] = true;
			$_SESSION['lunchdisabled'] = true;
			$_SESSION['statuscolor'] = "red";
			$_SESSION['message'] = "Clocked OUT";
			return;
		}
		else if($row['In_Out'] == "o" && $row['Brk_Lnch'] == "b"){
			$_SESSION['inoutdisabled'] = true;
			$_SESSION['lunchdisabled'] = true;
			$_SESSION['statuscolor'] = "#e5e600";
			$_SESSION['message'] = "Clocked Out on BREAK";
			return;
		}
		else if($row['In_Out'] == "o" && $row['Brk_Lnch'] == "l"){
			$_SESSION['inoutdisabled'] = true;
			$_SESSION['breakdisabled'] = true;
			$_SESSION['statuscolor'] = "#e68a00";
			$_SESSION['message'] = "Clocked Out on LUNCH";
			return;
		}
	}
	$_SESSION['message'] = "No Entry for Today";
	$_SESSION['statuscolor'] = "#4d4d4d";
}

function getRowID($ID){
	$db = Cnnct();
	$sth = $db->prepare("SELECT Row_ID FROM ROST_HIST_TIME WHERE ID = '".$ID."' AND DATE(Time_Cur) = DATE(CURDATE()) ORDER BY Time_Cur DESC LIMIT 1");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		return($row["Row_ID"]);
	}
}

function setRowID($ID){
	$curRow = getRowID($ID);
	$nextRow = $curRow + 1;
	return($nextRow);
}

function getPIN($username){
	$db = Cnnct();
	$sth = $db->prepare("SELECT ROST_EMPLOYEES.PIN FROM ROST_EMPLOYEES WHERE username = '".$username."'");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		return($row["PIN"]);
	}
}

function getID($username){
	$db = Cnnct();
	$sth = $db->prepare("SELECT ROST_EMPLOYEES.ID FROM ROST_EMPLOYEES WHERE username = '".$username."'");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		return($row["ID"]);
	}
}

function getFirstandLast($username){
	$db = Cnnct();
	$sth = $db->prepare("SELECT first_name, last_name FROM ROST_EMPLOYEES WHERE username = '".$username."' LIMIT 1");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		$_SESSION['first_name'] = $row['first_name'];
		$_SESSION['last_name'] = $row['last_name'];
	}
}

function getTimeQuery($ID){
	/*Values in ROST_HIST_TIME:
		Entry_ID, Time_Cur, In_Out, Brk_Lnch, Time_Change, Entry_Date, ID, Row_ID, Reason, Accepted, D_Reason */
		
	$db = Cnnct();
	$sth = $db->prepare("SELECT * FROM ROST_HIST_TIME WHERE ID = '".$ID."' AND DATE(Time_Cur) = DATE(CURDATE()) ORDER BY Time_Cur DESC LIMIT 1");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		return array('Entry_ID' => $row['Entry_ID'], 'Time_Cur' => $row['Time_Cur'], 'In_Out' => $row['In_Out'], 'Brk_Lnch' => $row['Brk_Lnch'], 'Time_Change' => $row['Time_Change'], 'Entry_Date' => $row['Entry_Date'], 'ID' => $row['ID'], 'Row_ID' => $row['Row_ID'], 'Reason' => $row['Reason'], 'Accepted' => $row['Accepted'], 'D_Reason' => $row['D_Reason']);
	}
}

function clocking($ID, $username, $attemptPIN){
	/* 
	CALLED BY THE INDEX PAGE
	gathers the employee information and last clock in/out information for the employee from the database.
	1.  if they press the clock in/out button
	2.  if there is no set clock in and clock out time for that day.
	*/
	
	$db = Cnnct(); //setup the db connection for those queries in the if statements

	getFirstandLast($username);
	$r3 = getTimeQuery($ID);
	$day_row_num = setRowID($ID);
	$actualPIN = getPIN($username);
	
	if($attemptPIN == $actualPIN){
		$curdttm = getCurDttm();
		if(!$r3){
			//clock them in for the start of their work day
			$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'i', '".$ID."', '1')");
			$sth->execute();
			//create array instead of session objects
			$_SESSION['message'] = "CLOCKED IN";
			$_SESSION['statuscolor'] = "#00e600";
			setUserFriendlyDate($ID);
		}
		//if they have already clocked out for the work day
		else if($r3['In_Out'] == "o" && !isset($r3['Brk_Lnch'])){
			//display the following error message
			$_SESSION['error'] = "You have already clocked out for the day, you cannot clock in a second time.";
		}
		//if they are clocked in, but not on break or lunch
		else if($r3['In_Out'] == "i" && !isset($r3['Brk_Lnch'])){
			//clock them out 
			$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'o', '".$ID."', '".$day_row_num."')");
			$sth->execute();
			$_SESSION['message'] = "CLOCKED OUT";
			$_SESSION['inoutdisabled'] = true;
			$_SESSION['breakdisabled'] = true;
			$_SESSION['lunchdisabled'] = true;
			$_SESSION['statuscolor'] = "red";
			setUserFriendlyDate($ID);
		}
		else if(isset($r3['Brk_Lnch'])){
			$_SESSION['error'] = "You need to clock back in from break or lunch before clocking out.";
		}
		else{
			$_SESSION['error'] = "Error Clocking In/Out.  Please see your supervisor.";
		}
	}
	else{
		CheckTime($ID);
		$_SESSION['error'] = "PIN is invalid; please try again";
	}
}

function lunch($ID, $username, $attemptPIN){
	
	//CALLED BY THE INDEX PAGE
	//gathers the employee information and last clock in/out information for the employee from the database.
	//require("timequery.php");
	$db = Cnnct(); //setup the db connection for those queries in the if statements
	getFirstandLast($username);
	$r3 = getTimeQuery($ID);
	$day_row_num = setRowID($ID);
	$actualPIN = getPIN($username);
	
	if($attemptPIN == $actualPIN){
		$curdttm = getCurDttm();
		if($r3['In_Out'] == "i" && !isset($r3['Brk_Lnch'])){
			//clock them out for lunch
			$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, Brk_Lnch, ID, Row_ID) VALUES('".$curdttm."', 'o', 'l', '".$ID."', '".$day_row_num."')");
			$sth->execute();
			$_SESSION['message'] = "Out on LUNCH.";
			$_SESSION['inoutdisabled'] = true;
			$_SESSION['breakdisabled'] = true;
			$_SESSION['statuscolor'] = "#e68a00";
			setUserFriendlyDate($ID);
		}
		//if they are currently clocked out AND...
		else if($r3['In_Out'] == "o"){
			//ON lunch when they press the lunch button
			if($r3['Brk_Lnch'] == "l"){
				//clock them back in from lunch
				$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'i', '".$ID."', '".$day_row_num."')");
				$sth->execute();
				$_SESSION['message'] = "In from LUNCH.";
				$_SESSION['statuscolor'] = "#00e600";
				setUserFriendlyDate($ID);
			}
			//ON break when they hit the lunch button
			else if($r3['Brk_Lnch'] == "b"){
				//display error message about clocking back in from break first.
				$_SESSION['error'] = "You are still on break, please clock back in from break before clocking out for lunch.";
			}
			else if(!isset($r3['Brk_Lnch'])){
				//display error message about being clocked out for the day already.
				$_SESSION['error'] = "You are already clocked out for the day.";
			}
		}
	}
	else{
		CheckTime($ID);
		$_SESSION['error'] = "PIN is invalid; please try again";
	}
}

function breaks($ID, $username, $attemptPIN){
	
	// CALLED BY THE INDEX PAGE
	//gathers the employee information and last clock in/out information for the employee from the database.
	$db = Cnnct(); //setup the db connection for those queries in the if statements
	getFirstandLast($username);
	$r3 = getTimeQuery($ID);
	$day_row_num = setRowID($ID);
	$actualPIN = getPIN($username);
	
	if($attemptPIN == $actualPIN){
		$curdttm = getCurDttm();
		//if they are clocked in and break or lunch is not set
		if($r3['In_Out'] == "i" && !isset($r3['Brk_Lnch'])){
			//clock them out for break
			$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, Brk_Lnch, ID, Row_ID) VALUES('".$curdttm."', 'o', 'b', '".$ID."', '".$day_row_num."')");
			$sth->execute();
			$_SESSION['message'] = "Out on BREAK";
			$_SESSION['inoutdisabled'] = true;
			$_SESSION['lunchdisabled'] = true;
			$_SESSION['statuscolor'] = "#e5e600";
			setUserFriendlyDate($ID);

		}
		//if they are currently clocked out AND...
		else if($r3['In_Out'] == "o"){
			//ON break when they press the break button
			if($r3['Brk_Lnch'] == "b"){
				//clock them back in from break
				$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'i', '".$ID."', '".$day_row_num."')");
				$sth->execute();
				$_SESSION['message'] = "In from BREAK";
				$_SESSION['statuscolor'] = "#00e600";
				setUserFriendlyDate($ID);
			}
			//ON lunch when they hit the break button
			else if($r3['Brk_Lnch'] == "l"){
				//display error message about clocking back in from lunch first.
				$_SESSION['error'] = "You are still on lunch, please clock back in from lunch before clocking out for break.";
			}
			else if(!isset($r3['Brk_Lnch'])){
				//display error message about being clocked out for the day already.
				$_SESSION['error'] = "You are already clocked out for the day.";
			}
		}
	}
	else{
		CheckTime($ID);
		$_SESSION['error'] = "PIN is invalid; please try again";
		//echo("<script> alert('".$_SESSION['error']."')</script>");
	}
}

function setLogin($ID, $username, $password){
	$db = Cnnct();
	if(!empty($username) && !empty($password)){ //if there is something in the text fields
		//execute the SQL query and return records
		$sth = $db->prepare("SELECT * FROM ROST_EMPLOYEES WHERE username = '".$username."' LIMIT 1");
		$sth->execute();
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		//fetch tha data from the database for at least one row.
		foreach($r as $row){
			if(empty($row)){ //if there is nothing brought back from the query
				$error['username'] = "That username is not listed.  Please try again."; //display that the username given did not come back in the query.
				require('view/single_sign_in.php');
			}
			else{ //else, if something was brought back from the query
				//************* SIGN IN SUCCESSFUL ***********************************
				if($row['password'] == $password){ //if the password given by the user matches the password in the database.
					//setcookie('username', $username, time()+86400, "/"); //set for auto-login for the rest of the day
					//setcookie('some_cookie', 'username', time() + 3600, "/");
					setcookie('username', $username, time()+(3600 * 24), "/");  // expire in 1 hour 
					getFirstandLast($username);
					CheckTime($ID);
					unset($_SESSION['error']);
					$_SESSION['signout_avail'] = true;
					require('view/template/header.php');
					require('view/home.php');
					return;
					//require('view/template/footer.php');
					//require('view/template/aside.php');
				}
				//*************** END SIGN IN SUCCESSFUL *********************************
				else{ //else, if the password given by the user DOES NOT match the password in the database.
					$error['password'] = "Your password is incorrect.  Please try again."; //display that the password DOES NOT match.
					unset($error['username']);
					require('view/single_sign_in.php');
					return;
				}
			}
		} //the actual query to be implemented.
		/*** START LOOK-UP FAIL ***/
		//This part comes up if the query does not produce any results (username and password do not match)
		$error['username'] = "That username is not listed.  Please try again."; //display that the username given did not come back in the query.
		require('view/single_sign_in.php');
		return;
		/*** END LOOK-UP FAIL ***/
	}
	else{
		if(empty($username)){ //if NO username was given
			$error['username'] = "Please input a user name"; //display that the user needs to input a username.
		}
		else{ // else, if the username was given.  
			$error['loginname'] = $username; //save the username value so that it can be kept in the input box when the page reloads.
		}
		
		if(empty($password)){ //if the NO password was given.
			$error['password'] = "Please input a password"; //display that the user needs to pinput a password.
		}
		else{
			$error['pwinput'] = $password;
		}
		require('view/single_sign_in.php'); //reload the page to display the error messages.
	}
}

function cookieLogin($ID, $username){
	$db = Cnnct();
	if(!empty($username)){ //if there is something in the text fields
		//execute the SQL query and return records
		$sth = $db->prepare("SELECT * FROM ROST_EMPLOYEES WHERE username = '".$username."' LIMIT 1"); //only select one row
		$sth->execute();
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		foreach($r as $row){
			if(empty($row)){ //if there is nothing brought back from the query
				$error['username'] = "That username is not listed.  Please try again."; //display that the username given did not come back in the query.
			}
			else{ //else, if something was brought back from the query
				getFirstandLast($username);
				CheckTime($ID);
				unset($_SESSION['error']);
				//require('view/template/header.php');
				require('view/home.php');
				//require('view/template/footer.php');
				//require('view/template/aside.php');
			}
		} //the actual query to be implemented.
	}
	else{
		require('view/single_sign_in.php'); //reload the page to display the error messages.
	}
}

function setUserFriendlyDate($ID){
	$display_time = getTimeQuery($ID); //get updated time
	$_SESSION['displaytime'] = date('h:i A', strtotime($display_time['Time_Cur']));  //set time for display in the home.php
}

function getCurDttm(){
	//date("Y-m-d H:i:s");
	//display server Date and Time and subtracting by 4 hours to get it to EST time.
	$curdttm = date("Y-m-d H:i:s", strtotime("-4 hours"));
	return $curdttm;
}

/**    Returns the offset from the origin timezone to the remote timezone, in seconds.
*    @param $remote_tz;
*    @param $origin_tz; If null the servers current timezone is used as the origin.
*    @return int;
*/
function get_timezone_offset($remote_tz, $origin_tz = null) {
    /*if($origin_tz === null) {
        if(!is_string($origin_tz = date_default_timezone_get())) {
            return false; // A UTC timestamp was returned -- bail out!
        }
	}
	echo('Origin TimeZone = '.$origin_tz);
	echo('Remote TimeZone = '.$remote_tz);
	$origin_dtz = new DateTimeZone($origin_tz);
	$remote_dtz = new DateTimeZone($remote_tz);
	$origin_dt = new DateTime("now", $origin_dtz);
	$remote_dt = new DateTime("now", $remote_dtz);
	$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	echo('Offset = '.$offset);
	return $offset;*/
	
$userTimezone = new DateTimeZone('America/New_York');
$gmtTimezone = new DateTimeZone('GMT');
$myDateTime = new DateTime('2016-03-21 13:14', $gmtTimezone);
$offset = $userTimezone->getOffset($myDateTime);
$myInterval=DateInterval::createFromDateString((string)$offset . 'seconds');
$myDateTime->add($myInterval);
$result = $myDateTime->format('Y-m-d H:i:s');
echo ($result);
}

// This will return 10800 (3 hours) ...
//$offset = get_timezone_offset('America/Los_Angeles','America/New_York');
// or, if your server time is already set to 'America/New_York'...
//$offset = get_timezone_offset('America/Los_Angeles');
// You can then take $offset and adjust your timestamp.
//$offset_time = time() + $offset;
?>