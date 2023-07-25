<?php
include_once  "db_connection.php";
require "util.php";

function init_crawler( $args): void {
	global $db;

	$table_name = 'crawl_last_chapter_link';

	try {
		$data = array(
			'post_id' => $args['post_id'],
			'link' => $args['chapter_link'],
			'link_next_chapter' => $args['chapter_link_next'],
			'novel_status' => $args['novel_status'],
			'novel_link' => $args['novel_link'],
			'crawl_status' => 'ready'
		);

		$db->insert($table_name, $data);

		echo json_encode(array("success" => true));

	} catch (Exception $e) {
		echo json_encode(array("success" => false, "data" => "Cant not init crawler"));
	}
}

function get_name( $chapter ) {
	if ( isset( $chapter['chapter_name_extend'] ) && strlen($chapter['chapter_name_extend']) > 0) {
		return $chapter['chapter_name'] . " - " . $chapter['chapter_name_extend'];
	} else {
		return $chapter['chapter_name'];
	}
}

function load_chapter(): array {
	global $db;

	$post_id = $_POST['manga_id'];
	$base_url = $_POST['base_url'];
	if (substr($base_url, -1) !== '/') {
		$base_url = $base_url . "/";
	}

	$sql = "SELECT chapter_id, chapter_name, chapter_name_extend, chapter_slug, date FROM wp_manga_chapters 
			WHERE post_id=" . $post_id . " order by chapter_index DESC";
	$result = $db->conn->query($sql);

	if ($result) {
		// Initialize an array to store the data
		$data = array();

		// Process the data
		while ($chapter = $result->fetch_assoc()) {
			$chapter['link'] = $base_url . $chapter['chapter_slug'] . "/";
			$chapter['full_name'] = get_name( $chapter );
			$data[] = $chapter;
		}

		// Free the result set
		$result->free();

		return $data;
	} else {
		return [];
	}
}