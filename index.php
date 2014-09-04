<?php
//------------------------------------------------------------------------------------------------------------------//

require_once 'classes/curl.class.php';
require_once 'classes/countboxexception.class.php';
require_once 'classes/countbox.class.php';

//------------------------------------------------------------------------------------------------------------------//

// Define default Countbox user login and password
// You can set different user data for a Countbox client using class constructor
define('C_COUNTBOX_LOGIN',    'user_login');
define('C_COUNTBOX_PASSWORD', 'user_password');

//------------------------------------------------------------------------------------------------------------------//

// Create a Countbox API client instance
$lCountbox = new Countbox();

// Get point list
$lPoints = $lCountbox->getPoints();

// Get yesterday attendance
$lYesterdayBegin = strtotime('yesterday');
$lYesterdayEnd = strtotime(date('Y-m-d 23:59:59', $lYesterdayBegin));
$lYesterdayAttendance = $lCountbox->getAttendance('all', $lYesterdayBegin, $lYesterdayEnd, 'day', true);

// echo all the points attendance and get visitors count on 15:00 yesterday
$lYesterday3pm = strtotime(date('Y-m-d 15:00:00', $lYesterdayBegin));
foreach($lPoints as &$lPoint)
{
	echo $lPoint['name'], PHP_EOL;
	foreach($lYesterdayAttendance as &$lPointAttendance)
	{
		if($lPointAttendance['id'] == $lPoint['id'])
		{
			echo "\t", 'Yesterday attendance in:', $lPointAttendance['in'], PHP_EOL;
			echo "\t", 'Yesterday attendance out:', $lPointAttendance['out'], PHP_EOL;
			break;
		}
	}

	echo "\t", 'Yesterday vititors count at 15:00:', $lCountbox->getVisitors($lPoint['id'], $lYesterday3pm), PHP_EOL;
}