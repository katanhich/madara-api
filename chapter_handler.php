<?php
include_once  "db_connection.php";
require "util.php";

function add_chapter( $args ): bool {
	global $db;
	$db->conn->begin_transaction();

	try {
		$result = insert_chapter( $args );

		$db->conn->commit();

		return $result;
	} catch ( Exception $e ) {
		$db->conn->rollback();

		$errorMessage = "Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
		error_log($errorMessage);

		return false;
	}
}

function insert_chapter($args): bool {
	global $db;

	$chapter_slug = slugify_text( $args['chapter_name'] );
	$chapter_seo = "Read " . $args['novel_name'] . " " . $args['chapter_name'] . " - "
	               . $args['chapter_name_extend'] . " online for free on Novel Saga";
	$data         = [
		'post_id' => $args['post_id'],
		'volume_id' => 0,
		'chapter_name' => $args['chapter_name'],
		'chapter_name_extend' => $args['chapter_name_extend'],
		'chapter_slug' => $chapter_slug,
		'date' => my_current_time( 'mysql' ),
		'date_gmt' => my_current_time( 'mysql', true ),
		'chapter_index' => $args['chapter_index'],
		'chapter_status' => 0,
		'chapter_seo' => $chapter_seo
	];

	$result = $db->insert( 'wp_manga_chapters', $data );
	if (!$result) {
		return false;
	}

	$chapter_id =  mysqli_insert_id($db->conn);
	$post_name = $chapter_id . '-' . $chapter_slug;
	$guid = "https://booksaga.org" . "/chapter_text_content/" . $post_name . "/";
	$data = array(
		'post_author'           => 1,
		'post_date'             => my_current_time( 'mysql' ),
		'post_date_gmt'         => my_current_time( 'mysql', true ),
		'post_content'          => $args['chapter_content'],
		'post_title'            => $post_name,
		'post_excerpt'          => '',
		'post_status'           => 'publish',
		'comment_status'        => 'open',
		'ping_status'           => 'closed',
		'post_name'             => $post_name,
		'to_ping'               => '',
		'pinged'                => '',
		'post_modified'         => my_current_time( 'mysql' ),
		'post_modified_gmt'     => my_current_time( 'mysql', true ),
		'post_content_filtered' => '',
		'post_parent'           => $chapter_id,
		'guid'                  => $guid,
		'menu_order'            => 0,
		'post_type'             => 'chapter_text_content'
	);

	$result = $db->insert( 'wp_posts', $data );
	if (!$result) {
		return false;
	}

	update_last_chapter_link($args);

	return true;
}

// todo remove this function
function update_last_chapter_link( $args): void {
	global $db;

	$table_name = 'crawl_last_chapter_link';

	$sql = "UPDATE $table_name SET link = ? WHERE post_id = ?";
	$stmt = $db->conn->prepare($sql);
	$stmt->bind_param("si", $args['chapter_link'], $args['post_id']);

	if ( !$stmt->execute() ) {
		return;
	}

	// neu chua co data thi insert vo
	if ( $stmt->affected_rows == 0 ) {
		try {
			$data = array(
				'post_id' => $args['post_id'],
				'link' => $args['chapter_link'],
				'novel_status' => $args['novel_status'],
				'novel_link' => $args['novel_link'],
			);
			$db->insert($table_name, $data);
		} catch (Exception $e) {

		}
	}
}

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