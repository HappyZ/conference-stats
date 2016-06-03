<?php
// HappyZ
// Last updated: Jun. 3, 2016

if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) 
{
	die( 'You are not running this correctly..' );
}

$updateFreq = 3600; // seconds
$updateRequired = false;
$currentT = time();

$confbrev = preg_replace("/[^a-zA-Z0-9]+/", "", htmlspecialchars($_POST["cb"]));
$paperurl = htmlspecialchars($_POST["pu"]);
$fullname = htmlspecialchars($_POST["fn"]);

if ( empty($confbrev) || empty($paperurl) && empty($fullname) )
{
	die( 'Incorrect parameters' );
}

require_once 'funcs/hotcrpFetch.php';
require_once 'funcs/misc.php';

// echo $confbrev."<br>".$paperurl."<br>".$fullname."<br>";

$namerow = getCurrentConfStats($confbrev);

if ( is_null($namerow) )
{
	// echo $currentT;
	$curNum = fetchCurNum($paperurl, "/paper.php/", null, 2000, 1);
	addRecord($confbrev, $paperurl, $curNum, $fullname, $currentT);
	$lastupdateTimeDeltaString = 'just now';
}
else
{
	// echo "<p>Prev: ".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";
	$paperurl = $namerow["paperurl"];
	if ( $namerow["lastupdate"] < ($currentT - $updateFreq) ) {
		// echo $currentT;
		$curNum = fetchCurNum($paperurl, "/paper.php/", null, 2000, $namerow["papercount"]);
		updateConfStats($confbrev, $curNum, $currentT);
		$namerow["lastupdate"] = $currentT;
	}
	else 
	{
		$curNum = $namerow["papercount"];
	}
	$lastupdateTimeDeltaString = lastupdateSentiment( $currentT - $namerow["lastupdate"] );
}

echo "{'confbrev':'".$confbrev."','papercount':".$curNum.",'lastupdate':'".$lastupdateTimeDeltaString."'}";

// $namerow = getCurrentConfStats($confbrev);
// echo "<p>New ".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";

?>
