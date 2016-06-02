<?php

/*
** Utilize MariaDB database for storing and fetching records
** Procedure to setup MariaDB is as follows:
** 0. Install MariaDB: yum install mariadb-server mariadb; and setup root password
** 1. First get in database: mysql -u root -p
** 2. Then create database: create database conferencestats;
** 3. use conferencestats
** 4. Create table named "stats": create table stats ( id int(5) not null auto_increment, confbrev varchar(20) not null, paperurl varchar(50) not null, papercount int(5) not null, fullname varchar(150) not null, primary key(id) );
** 5. Check data: DESCRIBE stats;
** 6. Insert example data: insert into stats (confbrev, paperurl, papercount, fullname) values ("nsdi16", "https://nsdi16.usenix.hotcrp.com", 1, "USENIX Symposium on Networked Systems Design and Implementation");
*/

function getCurrentConfNum($confbrev)
{
	$connect = new mysqli( "localhost", "someuser", "somepassword", "conferencestats" );
	if( $connect->connect_errno ) {
		exit( "Unable to connect to MariaDB:".$connect->connect_error );
	}
	$result=$connect->query( "SELECT * FROM stats WHERE confbrev = '".$confbrev."'" );
	if( !$result ) {
		$connect->close();
		return null;
	}
	return $result->fetch_assoc();
	$result->free();
	$connect->close();
}

$namerow = getCurrentConfNum("nsdi16");
if ( !is_null($namerow) ) 
{
	echo "<p>".$namerow[ "id" ]." | ".$namerow[ "confbrev" ]." | ".$namerow["paperurl"]." | ".$namerow["papercount"]."</p>";
}



?>
