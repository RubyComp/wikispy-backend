<?php

require_once __DIR__ . '/../Query.php';

Class LastRevId extends Query {

	public static function get_id() {

		$conn = self::get_connect('revs');

		$stmt = $conn->prepare(
			"SELECT MAX(id) FROM `revs` WHERE 1;"
		);
		
		if(!$stmt)
			ExceptionHandler::show_error("SQL Error: {$conn->errno} - {$conn->error}");

		$result = self::execute($conn, $stmt, 'get');
		$id = $result['data'][0]['MAX(id)'];

		return $id;
	}


}