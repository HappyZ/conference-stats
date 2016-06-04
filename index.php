<?php 
// HappyZ
// Last updated: Jun. 3, 2016

/* this page is index */
$thisPage = "index";
require_once 'funcs/misc.php';
require_once 'header.php'; 
?>

<ul id='conflist'>
<?php
$currentT = time();
$results = getAllStats();
while ( $namerow = $results->fetch_assoc() )
{
	echo "<a href='" . $namerow["paperurl"] . "' target='_blank'><li class='left'><span class='confbrev'>" . $namerow["confbrev"] . "</span><span class='fullname'>" . $namerow["fullname"] . "</span><span class='currentnumber'>" . $namerow["papercount"] . "</span><span class='lastupdate'>" . lastupdateSentiment( $currentT - $namerow["lastupdate"] ) . "</span></li></a>";
}
$results->free();
?>

<form id="ajaxform" class="left" action="ajax.php" method="post">
<div class="left">
	<input class="input_text" placeholder="Conference Abbreviation" type="text" name="cb" required>
	<input class="input_text" placeholder="Conference Full Name" type="text" name="fn">
	<input class="input_text" placeholder="Submission URL" type="text" name="pu">
</div>
<input class="input_submit left" type="submit" value="&#x2714;">
<span class="clear"></span>
</form>

</ul>

<div class="clear"></div>



<?php require_once 'footer.php'; ?>