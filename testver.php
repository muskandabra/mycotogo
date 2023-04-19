<?php

echo date_default_timezone_get();


$date = new DateTime();
$tz = $date->getTimezone();
echo $tz->getName();


echo phpinfo();

?>