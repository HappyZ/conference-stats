<?php

/*
** Utilize MariaDB database for storing and fetching records
** Procedure to setup MariaDB is as follows:
** 0. Install MariaDB: yum install mariadb-server mariadb; and setup root password
** 1. First get in database: mysql -u root -p
** 2. Then create database: create database conferencestats;
** 3. use conferencestats
** 4. Create table named "stats": create table stats ( id int(5) not null auto_increment, confbrev varchar(20) not null, paperurl varchar(50) not null, papercount int(5) not null, fullname varchar(150) not null, lastupdate int(13) not null, primary key(id) );
** 5. Check data: DESCRIBE stats;
** 6. Insert example data: insert into stats (confbrev, paperurl, papercount, fullname, lastupdate) values ("nsdi16", "https://nsdi16.usenix.hotcrp.com", 1, "USENIX Symposium on Networked Systems Design and Implementation", 1300000000);
*/

$database_username = "someuser";
$database_password = "somepassword";
$database_name = "conferencestats";

function getCurrentConfNum($confbrev)
{
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	$confbrev = $connect->real_escape_string($confbrev);
	if( $connect->connect_errno ) {
		exit( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result = $connect->query( "SELECT * FROM stats WHERE confbrev = '".$confbrev."'" );
	if( !$result ) {
		$connect->close();
		return null;
	}
	$curNum = $result->fetch_assoc();
	$result->free();
	$connect->close();
	return $curNum;
}

function updateCurrentConfNum($confbrev, $newPaperNum, $currentT)
{
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	$confbrev = $connect->real_escape_string($confbrev);
	$newPaperNum = $connect->real_escape_string($newPaperNum);
	$currentT = $connect->real_escape_string($currentT);
	if( $connect->connect_errno ) {
		exit( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result = $connect->query( "UPDATE stats SET papercount = ".$newPaperNum.", lastupdate = ".$currentT." WHERE confbrev = '".$confbrev."';" );
	if (!$result)
	{
		$connect->close();
		echo "Unable to connect to update the entry: ".$connect->connect_error;
	}
	$connect->close();
}

function addRecord($confbrev, $paperurl, $paperCount, $fullname, $currentT)
{
	global $database_username, $database_password, $database_name;
	$connect = new mysqli( "localhost", $database_username, $database_password, $database_name);
	$confbrev = $connect->real_escape_string($confbrev);
	$newPaperNum = $connect->real_escape_string($newPaperNum);
	$currentT = $connect->real_escape_string($currentT);
	if( $connect->connect_errno ) {
		exit( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result = $connect->query( "INSERT into stats (confbrev, paperurl, papercount, fullname, lastupdate) values ('".$confbrev."', '".$paperurl."', ".$paperCount.", '".$fullname."', ".$currentT.");" );
	if (!$result)
	{
		$connect->close();
		echo "Unable to connect to update the entry: ".$connect->connect_error;
	}
	$connect->close();
}



?>
