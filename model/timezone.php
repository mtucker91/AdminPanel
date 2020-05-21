<?php
$dateTime = new DateTime();
$dateTime->setTimeZone(new DateTimeZone('America/New_York'));
date_default_timezone_set('America/New_York');
$timezone = date_default_timezone_get();
echo "The current server timezone is: " . $timezone;
echo ($date = date('m/d/Y h:i:s a', time()));
echo($dateTime->format('T'));

?>