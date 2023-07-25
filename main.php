<?php
header('Content-Type: application/json');

if (!isset($_GET['x_action'])) {
	return;
}

require 'chapter_handler.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

	$action = $_GET['x_action'];
	$jsonData = file_get_contents('php://input');
	$requestData = json_decode($jsonData, true);

	if ( $action === 'init_crawler' ) {
		init_crawler($requestData);
		return;
	}

}
