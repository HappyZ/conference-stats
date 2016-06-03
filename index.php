<?php 
	/* this page is index */
	$thisPage = "index";
	require_once 'funcs/hotcrpFetch.php';
	require_once 'funcs/misc.php';
	require_once 'header.php'; 
?>

<?php 
	// specify:
	$confbrev = 'hotmobile16';
	$updateFreq = 3600; // seconds

	$namerow = getCurrentConfNum($confbrev);
	$currentT = time();
	$updateRequired = false;

	if ( is_null($namerow) )
	{
		$updateRequired = true;
		$paperurl = 'https://hotmobile16.hotcrp.com';
		$fullname = 'International Workshop on Mobile Computing Systems and Applications';
	}
	else
	{
		echo "<p>".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";
		$paperurl = $namerow["paperurl"];
		if ( $namerow["lastupdate"] < ($currentT - $updateFreq) ) $updateRequired = true;
	}

	if ( $updateRequired )
	{
		echo $currentT;
		$curNum = fetchCurNum($paperurl, "/paper/", null, 2000, 1);
		if ( is_null($namerow) )
			addRecord($confbrev, $paperurl, $curNum, $fullname, $currentT);
		else
			updateCurrentConfNum($confbrev, $curNum, $currentT);
	}
	
	$namerow = getCurrentConfNum($confbrev);
	echo "<p>".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]." | ".$namerow["lastupdate"]."</p>";
	
?>

<?php require_once 'footer.php'; ?>