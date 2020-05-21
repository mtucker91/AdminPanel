<?php
//NOTE: connect.php must be called on the page calling this page before-hand

function getUserInfo($username){
    $db = Cnnct();
	//execute the SQL query and return records
	$sth = $db->prepare("SELECT ID, first_name, last_name, lvl_ID, emp_pic_path FROM EMPLOYEES WHERE username = ? LIMIT 1");
	$sth->execute([$username]);
	//OR
	//$sth->execute(['username' => $username);
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	//fetch tha data from the database for at least one row.
	foreach($r as $row){
        return array('ID' => $row['ID'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'lvl_ID' => $row['lvl_ID'], 'emp_pic_path' => $row['emp_pic_path']);
	} //the actual query to be implemented.
}

//relies on getUserInfo() function
function giveUserFullName($resultrow){
    echo ($resultrow['first_name'].' '.$resultrow['last_name']);
}

//relies on getUserInfo() function
function giveUserID($resultrow){
    echo ($resultrow['ID']);
}

//relies on getUserInfo() function
function giveUserPicLoc($resultrow){
	if(isset($resultrow['emp_pic_path'])){
		echo($resultrow['emp_pic_path']);
	}
	else {
		echo("dist/img/default_profile.jpg");
	}
}

//relies on getUserLvlID() function
function getUserLvlID($resultrow){
    echo ($resultrow['lvl_ID']);
}

//function get
function getAccessLvl($lvl_ID, $echo){
	$db = Cnnct();
	$sth = $db->prepare("SELECT ID, lvl_nme, lvl_ord FROM ACCESS_LVL WHERE ID = ? LIMIT 1");
	$sth->execute([$lvl_ID]);
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	//fetch tha data from the database for at least one row.
	foreach($r as $row){
		if($echo == true){
			echo ($row['lvl_nme']);
		}
		else{
			return array('ID' => $row['ID'], 'lvl_nme' => $row['lvl_nme'], 'lvl_ord' => $row['lvl_ord']);
		}
        
	} //the actual query to be implemented.
}


function getMessageCount($ID){
	$db = Cnnct();
	$sth = $db->prepare("SELECT COUNT(*) AS count FROM MSG WHERE to_id = ?");
	$sth->execute([$ID]);
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	//fetch tha data from the database for at least one row.
	foreach($r as $row){
		return $row['count'];
	} //the actual query to be implemented.
}

function getMsgProfilePic($path){
	if(isset($path)){
		echo($path);
	}
	else {
		echo("dist/img/default_profile.jpg");
	}
}

function sidebarCollapse($ID){
	$db = Cnnct();
	$sth = $db->prepare("SELECT Sidebar_Closed FROM USER_PREF WHERE ID = ?");
	$sth->execute([$ID]);
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	//fetch tha data from the database for at least one row.
	foreach($r as $row){
		if($row['Sidebar_Closed'] == 1){
			return 'sidebar-collapse';
		}
	}
}


function getMsgTimeStampDiff($msgdate){

date_default_timezone_set('America/New_York');
//echo ($date = date('Y/m/d/ H:i:s', time()));


// Declare and define two dates 
$date1 = strtotime($msgdate);  
$date2 = strtotime(date('Y/m/d H:i:s', time())); //current datetime 

// Formulate the Difference between two dates 
$diff = abs($date2 - $date1);  


// To get the year divide the resultant date into 
// total seconds in a year (365*60*60*24) 
$years = floor($diff / (365*60*60*24));  


// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  


// To get the day, subtract it with years and  
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 


// To get the hour, subtract it with years,  
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));  


// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the  
// resultant date into total seconds i.e. 60 
$minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);  


// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes  
$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));  


if($years >= 1){
	if($years == 1){
		$retval = $years.' year';
	} else {
		$retval = $years.' years';
	}
}
else if($months >= 1){
	if($months == 1){
		$retval = $months.' month';
	} else {
		$retval = $months.' months';
	}
}
else if($days >= 1){
	if($days == 1){
		$retval = $days.' day';
	} else {
		$retval = $days.' days';
	}
}
else if($hours >= 1){
	if($hours == 1){
		$retval = $hours.' hour';
	} else {
		$retval = $hours.' hours';
	}
}
else if($minutes >= 1){
	if($minutes == 1){
		$retval = $minutes.' minute';
	} else {
		$retval = $minutes.' minutes';
	}
}
else if($seconds >= 1){
	if($seconds == 1){
		$retval = $seconds.' second';
	} else {
		$retval = $seconds.' seconds';
	}
}

return $retval;

}
?>