<?php
// HappyZ
// Last updated: Jun. 3, 2016
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) 
{
	die( json_encode(array('error'=>'You are not running this correctly..')) );
}

$updateFreq = 3600; // seconds
$maxNum = 2000;
$currentT = time();

$confbrev = preg_replace("/[^a-zA-Z0-9]+/", "", htmlspecialchars($_POST["cb"]));
$paperurl = htmlspecialchars($_POST["pu"]);
$fullname = htmlspecialchars($_POST["fn"]);

if ( empty($confbrev) )
{
	die( json_encode(array('error'=>'Incorrect parameters..')) );
}

require_once 'funcs/hotcrpFetch.php';
require_once 'funcs/misc.php';

// echo $confbrev."<br>".$paperurl."<br>".$fullname."<br>";

$namerow = getCurrentConfStats($confbrev);

if ( is_null($namerow) ) {
	if ( empty($paperurl) || empty($fullname) ) {
		die( json_encode(array('error'=>'Must specify url and full name..')) );
	}
	// get paper deadline
	$deadlines = fetchDeadline($paperurl);
	if (!is_array($deadlines)) {
		die( json_encode(array('error'=>$deadlines) ) );
	}
	// get current paper number
	$curNum = fetchCurNum($paperurl, "/paper.php/", null, $maxNum, 1);
	if (!is_numeric($curNum)) {
		die( json_encode(array('error'=>$curNum) ) );
	}
	$result = addRecord($confbrev, $paperurl, $curNum, $fullname, $currentT, $deadlines);
	if ($result !== "OK") {
		die( json_encode(array('error'=>$result) ) );
	}
	$lastupdateTimeDeltaString = 'just now';
} else {
	// echo "<p>Prev: ".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";
	$paperurl = $namerow["paperurl"];
	$fullname = $namerow["fullname"];
	if ( $namerow["lastupdate"] < ($currentT - $updateFreq) ) {
		// get current paper number
		$curNum = fetchCurNum($paperurl, "/paper.php/", null, $maxNum, $namerow["papercount"]);
		if (!is_numeric($curNum)) {
			die( json_encode(array('error'=>$curNum) ) );
		}
		$result = updateConfStats($confbrev, $curNum, $currentT);
		if ($result !== "OK") {
			die( json_encode(array('error'=>$result) ) );
		}
		$namerow["lastupdate"] = $currentT;
	} else {
		$curNum = $namerow["papercount"];
		$deadlines[] = $namerow["deadline_reg"];
		$deadlines[] = $namerow["deadline_sub"];
	}
	$lastupdateTimeDeltaString = lastupdateSentiment( $currentT - $namerow["lastupdate"] );
}

echo json_encode(array('cb'=>$confbrev, 'pc'=>$curNum, 'lu'=>$lastupdateTimeDeltaString, 
	'reg'=>$deadlines[0], 'sub'=>$deadlines[1], 'pu'=>$paperurl, 'fn'=>$fullname));

// $namerow = getCurrentConfStats($confbrev);
// echo "<p>New ".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";

?>
