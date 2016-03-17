<?php 
	/* this page is index */
	$thisPage = "index";
	require_once 'funcs/hotcrpFetch.php';
	require_once 'header.php'; 
?>

<?php 
	// specify:

	$hotcrpURL = //'conference url';
	$usr = //'username (email) address';
	$pwd = //'password';

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
	isPaperExist( $ch, $hotcrpURL, 500);
	curl_close($ch);
?>

<?php require_once 'footer.php'; ?>