<?php
include_once  "db_connection.php";
include_once  "util.php";



function test() {
	return true;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	echo "POST method is used.";
} else {
	echo my_current_time( 'mysql' ) . "\n";
	echo my_current_time( 'mysql', true ) . "\n";

	echo "GET method is used.";
}