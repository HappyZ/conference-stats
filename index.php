<?php 
// HappyZ
// Last updated: Jun. 3, 2016

/* this page is index */
$thisPage = "index";
require_once 'funcs/misc.php';
require_once 'header.php'; 
?>

<form action="ajax.php" method="post">
Conference Abbreviation: <input type="text" name="cb"><br>
Conference Full Name: <input type="text" name="fn"><br>
Submission URL: <input type="text" name="pu"><br>
<input type="submit">
</form>

<ul id='conflist'>
<?php
$currentT = time();
$results = getAllStats();
while ( $namerow = $results->fetch_assoc() )
{
	echo "<a href='" . $namerow["paperurl"] . "' target='_blank'><li class='left'><span class='confbrev'>" . $namerow["confbrev"] . "</span><span class='currentnumber'>" . $namerow["papercount"] . "</span><span class='lastupdate'>" . lastupdateSentiment( $currentT - $namerow["lastupdate"] ) . "</span><span class='fullname'>" . $namerow["fullname"] . "</span></li></a>";
}
$results->free();
?>
</ul>

<div class="clear"></div>

<?php require_once 'footer.php'; ?>