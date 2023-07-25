<?php
header('Content-Type: application/json');

require 'chapter_handler.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$jsonData = file_get_contents('php://input');
	$requestData = json_decode($jsonData, true);

	$action = $_GET['action'];

	if ( $action === 'add_chapter' ) {
		if ( add_chapter( $requestData ) ) {
			echo json_encode(array("success" => true));
		} else {
			echo json_encode(array("success" => false, "data" => "Cant not add chapter"));
		}
		return;
	} else if ( $action === 'init_crawler' ) {
		init_crawler($requestData);
		return;
	}

}

