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

	if (!isset($_POST['manga_id']) || !isset($_POST['base_url'])) {
		return [];
	}

	$post_id = $_POST['manga_id'];
	$base_url = $_POST['base_url'];

	if (substr($base_url, -1) !== '/') {
		$base_url = $base_url . "/";
	}

	$sql = "SELECT chapter_id, chapter_name, chapter_name_extend, chapter_slug, date FROM wp_manga_chapters 
			WHERE post_id=" . $post_id . " order by chapter_id DESC";
	$result = $db->conn->query($sql);

	if ($result) {
		$data = array();

		while ($chapter = $result->fetch_assoc()) {
			$chapter['link'] = $base_url . $chapter['chapter_slug'] . "/";
			$chapter['full_name'] = get_name( $chapter );
			$data[] = $chapter;
		}

		$result->free();

		return $data;
	} else {
		return [];
	}
}

function get_time_diff( $time, $timestamp = false ) {
	$check   = ! $timestamp ? strtotime( $time ) : $time;
	$current = my_current_time( 'timestamp' );

	if ( $current > $check + 259200 ) {
		$diff = date( 'F j, Y', strtotime($time) );
	} else {
		$diff = sprintf( '%s ago', human_time_diff( $check, $current ) );
	}

	return $diff;
//	return date( 'F j, Y', strtotime( $time ) );
}