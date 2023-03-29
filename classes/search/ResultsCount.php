<?php

class ResultsCount extends Search {

	public static function get_count(array $params) {

		$table = $params['table_content'];

		$conn = self::get_connect('content');
		$stmt = $conn->prepare(
			"SELECT COUNT(*)
			FROM `$table`
			WHERE content LIKE ?
			AND `$table`.ns
				IN (" . implode(',', $params['nspace']) . ")
			AND `$table`.`issub`
				IN (" . implode(',', $params['types']) . ");"
		);
		
		if(!$stmt)
			ExceptionHandler::show_error("SQL Error: {$conn->errno} - {$conn->error}");

		$stmt->bind_param(
			's',
			$params['text']
		);

		$result = self::execute($conn, $stmt, 'get');
		$count = $result['data'][0]['COUNT(*)'];

		return $count;
	}
}
