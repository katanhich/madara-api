<?php
include_once 'config.php';

class Database {
	public mysqli $conn;

	public function __construct() {
		global $servername, $username, $password, $dbname;

		$this->conn = new mysqli($servername, $username, $password, $dbname);

		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn ->connect_error);
		}
	}

	public function __destruct() {
		$this->conn->close();
	}

	public function insert($table_name, $data): bool {
		$keys = array_keys($data);
		$values = array_map(function ($value) {
			return addslashes($value);
		}, array_values($data));

		$sql = "INSERT INTO $table_name (" . implode(', ', $keys) . ") VALUES ('" . implode("', '", $values) . "')";

		if ( $this->conn->query( $sql ) == 1 ) {
			return true;
		} else {
			return false;
		}
	}

}

$db = new Database();