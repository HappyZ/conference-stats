<?php
// HappyZ
// Last updated: Jun. 4, 2016

/*
** Utilize MariaDB database for storing and fetching records
** Procedure to setup MariaDB is as follows:
** 0. Install MariaDB: yum install mariadb-server mariadb; and setup root password
** 1. First get in database: mysql -u root -p
** 2. Then create database: create database conferencestats
** 3. GRANT ALL ON conferencestats.* TO 'someuser' IDENTIFIED BY 'somepassword';
** 4. use conferencestats
** 5. Create table named "stats": create table stats ( id int(5) not null auto_increment, confbrev varchar(20) not null, paperurl varchar(50) not null, papercount int(5) not null, fullname varchar(150) not null, lastupdate int(13) not null, primary key(id) );
** 6. Check data: DESCRIBE stats;
** 7. Insert example data: insert into stats (confbrev, paperurl, papercount, fullname, lastupdate) values ("nsdi16", "https://nsdi16.usenix.hotcrp.com", 1, "USENIX Symposium on Networked Systems Design and Implementation", 1300000000);
*/

$database_username = "someuser";
$database_password = "somepassword";
$database_name = "conferencestats";

function initializeTable() {
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	if( $connect->connect_errno ) {
		die( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$test = $connect->query( "select id from stats limit 1" );
	if (empty($test)) {
		$connect->query( "CREATE TABLE stats ( id INT(5) NOT NULL auto_increment, confbrev VARCHAR(20) NOT NULL, paperurl VARCHAR(50) NOT NULL, papercount INT(5) NOT NULL, fullname VARCHAR(150) NOT NULL, lastupdate INT(10) NOT NULL, deadline_reg INT(10) NOT NULL, deadline_sub INT(10) NOT NULL, PRIMARY KEY(id) );" );
	}
	$connect->close();
}

function getAllStats() {
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	if( $connect->connect_errno ) {
		die( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result = $connect->query( "SELECT * FROM stats;" );
	$connect->close();
	return $result;
}

function getCurrentConfStats($confbrev) {
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	$confbrev = $connect->real_escape_string($confbrev);
	if( $connect->connect_errno ) {
		exit( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result = $connect->query( "SELECT * FROM stats WHERE confbrev = '".$confbrev."';" );
	if( !$result ) {
		$connect->close();
		return null;
	}
	$curNum = $result->fetch_assoc();
	$result->free();
	$connect->close();
	return $curNum;
}

function updateConfStats($confbrev, $newPaperNum, $currentT)
{
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	$confbrev = $connect->real_escape_string($confbrev);
	$newPaperNum = $connect->real_escape_string($newPaperNum);
	$currentT = $connect->real_escape_string($currentT);
	if( $connect->connect_errno ) {
		exit( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result = $connect->query( "UPDATE stats SET papercount=".$newPaperNum.",lastupdate=".$currentT." WHERE confbrev = '".$confbrev."';" );
	if (!$result) {
		$feedback = "Unable to connect to update the entry: ".$connect->connect_error;
	} else {
		$feedback = "OK";
	}
	$connect->close();
	return $feedback;
}

function addRecord($confbrev, $paperurl, $paperCount, $fullname, $currentT, $deadlines)
{
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	$confbrev = $connect->real_escape_string($confbrev);
	$newPaperNum = $connect->real_escape_string($newPaperNum);
	$currentT = $connect->real_escape_string($currentT);
	if( $connect->connect_errno ) {
		exit( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result = $connect->query( "INSERT into stats (confbrev,paperurl,papercount,fullname,lastupdate,deadline_reg,deadline_sub) values ('".$confbrev."','".$paperurl."',".$paperCount.",'".$fullname."',".$currentT.",".$deadlines[0].",".$deadlines[1].");" );
	if (!$result) {
		$feedback = "Unable to connect to update the entry: ".$connect->connect_error;
	} else {
		$feedback = "OK";
	}
	$connect->close();
	return $feedback;
}

function lastupdateSentiment($lastupdateTimeDelta)
{
	if ( $lastupdateTimeDelta < 1 )
	{
		$lastupdateTimeDeltaString = 'just now';
	}
	elseif ( $lastupdateTimeDelta < 60 )
	{
		$lastupdateTimeDeltaString = $lastupdateTimeDelta.' seconds ago';
	}
	elseif ( $lastupdateTimeDelta < 3600 )
	{
		$lastupdateTimeDeltaString = round($lastupdateTimeDelta/60).' minutes ago';
	}
	elseif ( $lastupdateTimeDelta < 86400 )
	{
		$lastupdateTimeDeltaString = round($lastupdateTimeDelta/3600).' hours ago';
	}
	elseif ( $lastupdateTimeDelta < 432000 )
	{
		$lastupdateTimeDeltaString = round($lastupdateTimeDelta/86400).' days ago';
	}
	else
	{
		$lastupdateTimeDeltaString = 'long time ago';
	}
	return $lastupdateTimeDeltaString;
}


?>
