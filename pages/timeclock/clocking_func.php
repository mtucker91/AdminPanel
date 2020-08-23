<?php

function CheckTime($ID, $curdttm){
	/*
	results numbers:
	[0] = message for the user to read
	[1] = alert box class and model information for the way the alert box appears for the user's message.
	[2] = Current status message
	[3] = Current status color
	[4] = clock in/out option disabled status
	[5] = break option disabled status
	[6] = lunch option disabled status
	[7] = Current User Friendly Time
	*/
	$db = Cnnct();
	$curtime = setUserFriendlyTime($ID);
	$sth = $db->prepare("SELECT * FROM ROST_HIST_TIME WHERE ID = '".$ID."' AND DATE(Time_Cur) = DATE('".$curdttm."') ORDER BY Time_Cur DESC LIMIT 1");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	$resultarr = array(0 => NULL, 1 => NULL);
	foreach($r as $row){
		if($row['In_Out'] == "i" && !isset($row['Brk_Lnch'])){
			$resultarr[2] = "CLOCKED IN";
			$resultarr[3] = "#00e600";
			$resultarr[4] = false;
			$resultarr[5] = false;
			$resultarr[6] = false;
		}
		else if($row['In_Out'] == "o" && !isset($row['Brk_Lnch'])){
			$resultarr[2] = "Clocked OUT";
			$resultarr[3] = "red";
			$resultarr[4] = true;
			$resultarr[5] = true;
			$resultarr[6] = true;
		}
		else if($row['In_Out'] == "o" && $row['Brk_Lnch'] == "b"){
			$resultarr[2] = "Out on BREAK";
			$resultarr[3] = "#e5e600";
			$resultarr[4] = true;
			$resultarr[5] = false;
			$resultarr[6] = true;
		}
		else if($row['In_Out'] == "o" && $row['Brk_Lnch'] == "l"){
			$resultarr[2] = "Out on LUNCH";
			$resultarr[3] = "#e68a00";
			$resultarr[4] = true;
			$resultarr[5] = true;
			$resultarr[6] = false;
		}
		$resultarr[7] = $curtime;
		return($resultarr);
	}
	$resultarr[2] = "No Entry for Today";
	$resultarr[3] = "#4d4d4d";
	$resultarr[4] = false;
	$resultarr[5] = true;
	$resultarr[6] = true;
	$resultarr[7] = NULL;
	return($resultarr);
}

function getRowID($ID){
	$db = Cnnct();
	$curdttm = getCurDttm(false);
	$sth = $db->prepare("SELECT Row_ID FROM ROST_HIST_TIME WHERE ID = '".$ID."' AND DATE(Time_Cur) = DATE('".$curdttm."') ORDER BY Time_Cur DESC LIMIT 1");
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

function getPIN(string $username){
	$db = Cnnct();
	$sth = $db->prepare("SELECT ROST_EMPLOYEES.PIN FROM ROST_EMPLOYEES WHERE username = '".$username."'");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		return($row["PIN"]);
	}
}

function getID(string $username){
	$db = Cnnct();
	$sth = $db->prepare("SELECT ROST_EMPLOYEES.ID FROM ROST_EMPLOYEES WHERE username = '".$username."'");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		return($row["ID"]);
	}
}

function getFirstandLast(string $username){
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
	$curdttm = getCurDttm(false);
	$sth = $db->prepare("SELECT * FROM ROST_HIST_TIME WHERE ID = '".$ID."' AND DATE(Time_Cur) = DATE('".$curdttm."') ORDER BY Time_Cur DESC LIMIT 1");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach($r as $row){
		return array('Entry_ID' => $row['Entry_ID'], 'Time_Cur' => $row['Time_Cur'], 'In_Out' => $row['In_Out'], 'Brk_Lnch' => $row['Brk_Lnch'], 'Time_Change' => $row['Time_Change'], 'Entry_Date' => $row['Entry_Date'], 'ID' => $row['ID'], 'Row_ID' => $row['Row_ID'], 'Reason' => $row['Reason'], 'Accepted' => $row['Accepted'], 'D_Reason' => $row['D_Reason']);
	}
}

function getDisabledStat(bool $disarrayval){
	if($disarrayval == true){
		echo('disabled');
	}
}

function setSelectedStat($idisabled = null, $bdisabled = null, $ldisabled = null, $ord){
	if($ord == 1){
		if($idisabled == true && $bdisabled == true && $ldisabled == true){
			echo('selected');
		}
	}
	// $ord following array value for disabled
	else if($ord == 4){
		if($idisabled == false && $bdisabled == true && $ldisabled == true){
			echo('selected');
		}
	}
	else if($ord == 5){
		if($idisabled == true && $bdisabled == false && $ldisabled == true){
			echo('selected');
		}
	}
	else if($ord == 6){
		if($idisabled == true && $bdisabled == true && $ldisabled == false){
			echo('selected');
		}
	}
}

function old_getCurDttm(){
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
function getCurDttm(bool $getTime) {
	$userTimezone = new DateTimeZone('America/New_York');
	//echo($userTimezone);
	$gmtTimezone = new DateTimeZone('GMT');
	//echo($gmtTimezone);
	//$myDateTime = new DateTime('2016-03-21 13:14', $gmtTimezone);
	$myDateTime = new DateTime(date("Y-m-d h:i:s"), $gmtTimezone);
	//echo('myDateTime variable '.$myDateTime);
	$offset = $userTimezone->getOffset($myDateTime);
	//echo('offset variable '.$offset);
	$myInterval=DateInterval::createFromDateString((string)$offset . 'seconds');
	//echo('myInterval variable '.$myInterval);
	$myDateTime->add($myInterval);
	//echo('myDateTime variable with myInterval added '.$myDateTime);
	if($getTime == true){
		$result = $myDateTime->format('Y-m-d H:i:s');
	}
	else if($getTime == false){
		$result = $myDateTime->format('Y-m-d');
	}
	//echo ('the resulting dttm '.$result);
	//echo("from getCurDttm function ".$result);
	return($result);
}


/*function get_timezone_offset($remote_tz, $origin_tz = null) {

	
$userTimezone = new DateTimeZone('America/New_York');
$gmtTimezone = new DateTimeZone('GMT');
//$myDateTime = new DateTime('2016-03-21 13:14', $gmtTimezone);
$myDateTime = new DateTime(date("Y-m-d h:i:s"), $gmtTimezone);

$offset = $userTimezone->getOffset($myDateTime);

$myInterval=DateInterval::createFromDateString((string)$offset . 'seconds');

$myDateTime->add($myInterval);

$result = $myDateTime->format('Y-m-d H:i:s');
echo ($result);
}*/


function setUserFriendlyTime($ID){
	$display_time = getTimeQuery($ID); //get updated time
	if(isset($display_time)){
		$resulttime = date('h:i A', strtotime($display_time['Time_Cur']));  //set time for display in the home.php
		return($resulttime);
	}
	else{
		return(NULL);
	}	
}

function clocking($ID, $username, $attemptPIN, $timeoption){
	/* 
	CALLED BY THE INDEX PAGE
	gathers the employee information and last clock in/out information for the employee from the database.
	1.  if they press the clock in/out button
	2.  if there is no set clock in and clock out time for that day.

	results numbers:
	[0] = message for the user to read
	[1] = alert box class and model information for the way the alert box appears for the user's message.
	[2] = Current status message
	[3] = Current status color
	[4] = clock in/out option disabled status
	[5] = break option disabled status
	[6] = lunch option disabled status
	[7] = Current User Friendly Time
	*/
	$db = Cnnct(); //setup the db connection for those queries in the if statements
	$resultarr = array();
	//getFirstandLast($username);
	$r3 = getTimeQuery($ID);
	//echo("In_Out status = ".$r3['In_Out']."; Brk_Lnch status = ".$r3['Brk_Lnch']);
	$day_row_num = setRowID($ID);
	$actualPIN = getPIN($username);
	if($attemptPIN == $actualPIN){
		$curdttm = getCurDttm(true);
		//echo($curdttm);
			if($timeoption == "ClockIn"){
				if(!$r3){
					//clock them in for the start of their work day
					$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'i', '".$ID."', '1')");
					$sth->execute();
					//create array instead of session objects
					$resultarr[0] = "Successfully Clocked In";
					$resultarr[1] = "success";
					$resultarr[2] = "CLOCKED IN";
					$resultarr[3] = "#00e600"; //green
					$resultarr[4] = false;
					$resultarr[5] = false;
					$resultarr[6] = false;
				}
				//if they have already clocked out for the work day
				else if($r3['In_Out'] == "o" && !isset($r3['Brk_Lnch'])){
					//display the following error message
					$resultarr[0] = "You have already clocked out for the day, you cannot clock in a second time.";
					$resultarr[1] = "warning";
				}
				//if they are clocked in, but not on break or lunch
				else if($r3['In_Out'] == "i" && !isset($r3['Brk_Lnch'])){
					//clock them out 
					$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'o', '".$ID."', '".$day_row_num."')");
					$sth->execute();
					$resultarr[0] = "Successfully Clocked Out!";
					$resultarr[1] = "success";
					$resultarr[2] = "CLOCKED OUT";
					$resultarr[3] = "red"; //red
					$resultarr[4] = true;
					$resultarr[5] = true;
					$resultarr[6] = true;
				}
				else if(isset($r3['Brk_Lnch'])){
					$resultarr[0] = "You need to clock back in from break or lunch before clocking out.";
					$resultarr[1] = "warning";
				}
				else{
					$resultarr[0] = "Error Clocking In/Out.  Please see your supervisor.";
					$resultarr[1] = "alert";
				}
			}
			else if($timeoption == "Break"){
				
				//if they are clocked in and break or lunch is not set
				if($r3['In_Out'] == "i" && !isset($r3['Brk_Lnch'])){
					//clock them out for break
					$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, Brk_Lnch, ID, Row_ID) VALUES('".$curdttm."', 'o', 'b', '".$ID."', '".$day_row_num."')");
					$sth->execute();
					$resultarr[0] = "Clocked out on Break successfully";
					$resultarr[1] = "success";
					$resultarr[2] = "Out on BREAK";
					$resultarr[3] = "#e5e600"; //yellow-ish
					$resultarr[4] = true;
					$resultarr[5] = false;
					$resultarr[6] = true;
				}
				//if they are currently clocked out AND...
				else if($r3['In_Out'] == "o"){
					//ON break when they press the break button
					if($r3['Brk_Lnch'] == "b"){
						//clock them back in from break
						$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'i', '".$ID."', '".$day_row_num."')");
						$sth->execute();
						$resultarr[0] = "Clocked back in from Break successfully";
						$resultarr[1] = "success";
						$resultarr[2] = "In from BREAK";
						$resultarr[3] = "#00e600"; //green
						$resultarr[4] = false;
						$resultarr[5] = false;
						$resultarr[6] = false;
					}
					//ON lunch when they hit the break button
					else if($r3['Brk_Lnch'] == "l"){
						//display error message about clocking back in from lunch first.
						$resultarr[0] = "You are still on lunch, please clock back in from lunch before clocking out for break.";
						$resultarr[1] = "warning";
					}
					else if(!isset($r3['Brk_Lnch'])){
						//display error message about being clocked out for the day already.
						$resultarr[0] = "You are already clocked out for the day.";
						$resultarr[1] = "alert";
					}
				}
			}
			else if($timeoption == "Lunch"){
				if($r3['In_Out'] == "i" && !isset($r3['Brk_Lnch'])){
					//clock them out for lunch
					$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, Brk_Lnch, ID, Row_ID) VALUES('".$curdttm."', 'o', 'l', '".$ID."', '".$day_row_num."')");
					$sth->execute();
					$resultarr[0] = "Clocked out on Lunch successfully";
					$resultarr[1] = "success";
					$resultarr[2] = "Out on LUNCH.";
					$resultarr[3] = "#e68a00"; //orange
					$resultarr[4] = true;
					$resultarr[5] = true;
					$resultarr[6] = false;
				}
				//if they are currently clocked out AND...
				else if($r3['In_Out'] == "o"){
					//ON lunch when they press the lunch button
					if($r3['Brk_Lnch'] == "l"){
						//clock them back in from lunch
						$sth = $db->prepare("INSERT INTO ROST_HIST_TIME (Time_Cur, In_Out, ID, Row_ID) VALUES('".$curdttm."', 'i', '".$ID."', '".$day_row_num."')");
						$sth->execute();
						$resultarr[0] = "Clocked back in from Lunch successfully"; //orange
						$resultarr[1] = "success";
						$resultarr[2] = "In from LUNCH.";
						$resultarr[3] = "#00e600"; //green
						$resultarr[4] = false;
						$resultarr[5] = false;
						$resultarr[6] = false;
					}
					//ON break when they hit the lunch button
					else if($r3['Brk_Lnch'] == "b"){
						//display error message about clocking back in from break first.
						$resultarr[0] = "You are still on break, please clock back in from break before clocking out for lunch.";
						$resultarr[1] = "warning";
					}
					else if(!isset($r3['Brk_Lnch'])){
						//display error message about being clocked out for the day already.
						$resultarr[0] = "You are already clocked out for the day.";
						$resultarr[1] = "alert";
					}
				}
			}
			else{
				$resultarr[0] = "No option selected for the timeclock.  Please try again";
				$resultarr[1] = "warning";
				$resultarr[2] = "";
				$resultarr[3] = "";
				$resultarr[4] = false;
				$resultarr[5] = false;
				$resultarr[6] = false;
				
			}
	}
	else{
		$resultarr[0] = "PIN is invalid; please try again";
		$resultarr[1] = "danger";
	}
	$curtime = setUserFriendlyTime($ID);
	$resultarr[7] = $curtime;
	return(json_encode($resultarr));
	
}

//only returns the last row created for that date NEED TO FIX
function allTimingsDay ($ID, $curdttm) {
	$db = Cnnct(); //setup the db connection for those queries in the if statements
	$curdttm = getCurDttm(false);
	$resultarr = array();
	$sth = $db->prepare("SELECT Row_ID, Time_Cur, In_Out, Brk_Lnch FROM ROST_HIST_TIME WHERE ID = '".$ID."' AND DATE(Time_Cur) = DATE('".$curdttm."') ORDER BY Row_ID");
	$sth->execute();
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	$i = 0;
	foreach($r as $row){
		$resultarr[$i] = array('Row_ID' => $row['Row_ID'], 'Time_Cur' => $row['Time_Cur'], 'In_Out' => $row['In_Out'], 'Brk_Lnch' => $row['Brk_Lnch']);
		//to check what they came back in from prior
		if($resultarr[$i]['In_Out'] == 'i' && $resultarr[$i]['Row_ID'] != 1){
			$j = $i - 1;  //get the row before the current row to see what they came back from
			//if they came back in from a break previously
			if($resultarr[$j]['Brk_Lnch'] == 'b'){
				$resultarr[$i]['Status'] = "Clocked In from Break";
			}
			//if they came back from a lunch previously
			else if($resultarr[$j]['Brk_Lnch'] == 'l'){
				$resultarr[$i]['Status'] = "Clocked In from Lunch";
			}
		}
		else{
			//if they clocked out for any reason
			if($resultarr[$i]['In_Out'] == 'o'){
				//if they clocked out for break
				if($resultarr[$i]['Brk_Lnch'] == 'b'){
					$resultarr[$i]['Status'] = "Clocked Out for Break";
				}
				//if they clocked out for lunch
				else if($resultarr[$i]['Brk_Lnch'] == 'l'){
					$resultarr[$i]['Status'] = "Clocked Out for Lunch";
				}
				//if they clocked out for the day
				else{
					$resultarr[$i]['Status'] = "Clocked Out For the Day";
				}
			}
			//if the status shows as "Clocked In" but is the first row for the day
			else{
				$resultarr[$i]['Status'] = "Clocked In";
			}
		}
		$i += 1;
	}
	return $resultarr;
}

function getStartingVars(){

}

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

?>