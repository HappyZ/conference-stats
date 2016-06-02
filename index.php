<?php 
	/* this page is index */
	$thisPage = "index";
	require_once 'funcs/hotcrpFetch.php';
	require_once 'funcs/misc.php';
	require_once 'header.php'; 
?>

<?php 
	// specify:
	$hotcrpURL = //'conference url';
	$usr = //'username (email) address';
	$pwd = //'password';
	
	$fileName = 'nsdi16';
	
	if ( file_exists( $cachePATH.$fileName ) ) 
	{
		$info = json_decode( ( file_get_contents( $cachePATH.$fileName ) ), true );
		$curNum = $info["curNum"];
	}
	else
	{
		$ch = curl_init();
		$err = initializeFetch( $ch, $hotcrpURL, $usr, $pwd );
		if ( $err == 1)
		{
			die('This is NOT hotcrp');
		}
		if ( $err == 2)
		{
			die('Cannot find 8-char postcode');
		}
		if ( $err == 3)
		{
			die('Cannot log in');
		}
		$curNum = searchMaxPaperNum( $ch, $hotcrpURL, "/paper/", null, 2000, 1);
		$info = array( 'curNum' => $curNum );
		$fp = fopen($cachePATH.$fileName, 'w');
		fwrite( $fp, json_encode( $info ) );
		fclose( $fp );
	// 	isPaperExist( $ch, $hotcrpURL, 500);
		curl_close($ch);
	}
	echo $curNum;
?>

<?php require_once 'footer.php'; ?>