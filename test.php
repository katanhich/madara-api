<?php
include_once  "db_connection.php";
include_once  "util.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	echo "POST method is used.";
} else {
	echo "GET method is used.";
}