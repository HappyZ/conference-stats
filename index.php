<?php 
// HappyZ
// Last updated: Jun. 3, 2016

/* this page is index */
$thisPage = "index";
require_once 'funcs/misc.php';
initializeTable();
require_once 'header.php'; 
?>

<ul id='conflist'>
<?php
$currentT = time();
$results = getAllStats();
if (!empty($results)) {
	while ( $namerow = $results->fetch_assoc() ) {
		$dreg = new DateTime("@".$namerow['deadline_reg'], new DateTimeZone('UTC'));
		$dreg->setTimezone(new DateTimeZone(date_default_timezone_get()));
		$dsub = new DateTime("@".$namerow['deadline_sub'], new DateTimeZone('UTC'));
		$dsub->setTimezone(new DateTimeZone(date_default_timezone_get()));
		echo "<a href='" . $namerow["paperurl"] . "' target='_blank'><li class='left'><span class='confbrev'>" . $namerow["confbrev"] . "</span><span class='fullname'>" . $namerow["fullname"] . "</span><span class='currentnumber'>" . $namerow["papercount"] . "</span><span class='deadlines'>Registration Due: " . $dreg->format('D, M d, Y g:i:s A') . "</span><span class='deadlines'>Submission Due: " . $dsub->format('D, M d, Y g:i:s A') . "</span><span class='lastupdate'>Updated " . lastupdateSentiment( $currentT - $namerow["lastupdate"] ) . "</span></li></a>";
	}
	$results->free();
}
?>

<form id="ajaxform" class="left" action="ajax.php" method="post">
<div class="left">
	<input class="input_text" placeholder="Conference Abbreviation" type="text" name="cb" required>
	<input class="input_text" placeholder="Conference Full Name" type="text" name="fn">
	<input class="input_text" placeholder="Submission URL" type="text" name="pu">
</div>
<input class="input_submit left" type="submit" value="&#x2714;">
<span class="clear"></span>
<span id="warningwords"></span>
</form>

</ul>

<div class="clear"></div>

<?php require_once 'footer.php'; ?>