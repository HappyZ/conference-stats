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
	// echo $currentT;
	$curNum = fetchCurNum($paperurl, "/paper.php/", null, $maxNum, 1);
	if (!is_numeric($curNum)) {
		die( json_encode(array('error'=>$curNum) ) );
	}
	addRecord($confbrev, $paperurl, $curNum, $fullname, $currentT);
	$lastupdateTimeDeltaString = 'just now';
} else {
	// echo "<p>Prev: ".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";
	$paperurl = $namerow["paperurl"];
	if ( $namerow["lastupdate"] < ($currentT - $updateFreq) ) {
		// echo $currentT;
		$curNum = fetchCurNum($paperurl, "/paper.php/", null, $maxNum, $namerow["papercount"]);
		if (!is_numeric($curNum)) {
			die( json_encode(array('error'=>$curNum) ) );
		}
		updateConfStats($confbrev, $curNum, $currentT);
		$namerow["lastupdate"] = $currentT;
	} else {
		$curNum = $namerow["papercount"];
	}
	$lastupdateTimeDeltaString = lastupdateSentiment( $currentT - $namerow["lastupdate"] );
}

echo json_encode(array('confbrev'=>$confbrev, 'papercount'=>$curNum, 'lastupdate'=>$lastupdateTimeDeltaString));

// $namerow = getCurrentConfStats($confbrev);
// echo "<p>New ".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";

?>
