<?php

class Query {

	protected static $config = [
		'pages-table' => 'pages',
		'content-table' => 'content',
		'max-show' => 500
	];

	protected static function push_result($result) {
		$data = [];
		while ($row = $result->fetch_assoc())
			array_push($data, $row);
		return $data;
	}

	protected static function get_connect($base) {

		switch ($base) {
			// case 'main':
			// 	$conn = new mysqli(
			// 		SERVERNAME,
			// 		USERNAME,
			// 		PASSWORD,
			// 		DBNAME
			// 	);
			// 	break;
			case 'content':
				$conn = new mysqli(
					SERVERNAME_C,
					USERNAME_C,
					PASSWORD_C,
					DBNAME_C
				);
				break;
			case 'revs':
				$conn = new mysqli(
					SERVERNAME_R,
					USERNAME_R,
					PASSWORD_R,
					DBNAME_R
				);
				break;
			default:
				ExceptionHandler::show_error("Uncurrect connection type: {$base}");
		}

		if ($conn->connect_error)
			ExceptionHandler::show_error("Connection failed for {$base}: {$conn->connect_error}");

		$conn->set_charset(CHARSET);

		return $conn;
	}

	protected static function execute($conn, $stmt, $mode = '') {
		$success = $stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		$conn->close();

		$response = [
			'success' => $success,
		];

		if ($mode == 'get')
			$response['data'] = self::push_result($result);

		return $response;
	}

	public static function prepare_query($conn, $sql, $data = false) {
		$stmt = $conn->prepare($sql);

		if(!$stmt)
			ExceptionHandler::show_error("SQL Error: {$conn->errno} - {$conn->error}");

		if ($data)
			$stmt->bind_param(...$data);

		return $stmt;
	}

	// public static function get_pages_by_id_range(int $at, int $to) {

	// 	$conn = self::get_connect('main');

	// 	$stmt = $conn->prepare(
	// 		"SELECT id, title, lastrev FROM `pages` WHERE id BETWEEN ? AND ?"
	// 	);
	// 	$stmt->bind_param("ii", $at, $to);

	// 	return self::execute($conn, $stmt, 'get');

	// }

	// public static function get_max_page_id() {

	// 	$conn = self::get_connect('main');
	// 	$stmt = $conn->prepare(
	// 		"SELECT MAX(id) FROM `pages`"
	// 	);

	// 	$id = self::execute($conn, $stmt, 'get')
	// 		['data'][0]['MAX(id)'];

	// 	if (!(is_numeric($id) && (int)$id > 0))
	// 		$id = 0;

	// 	return $id;
	// }

	// public static function get_revs_by_mounths() {
	// 	/* TODO */
	// 	$conn = self::get_connect('main');
	// 	$stmt = $conn->prepare(
	// 		"SELECT
	// 			YEAR(timestamp) AS year,
	// 			MONTH(timestamp) AS month,
	// 			COUNT(*) AS count
	// 		FROM
	// 			`ru-fo-revs` ft
	// 			JOIN `ru-fo-users` ut ON ft.userid = ut.id
	// 		WHERE
	// 			ft.missing = 0
	// 		GROUP BY
	// 			YEAR(timestamp),
	// 			MONTH(timestamp)
	// 		HAVING
	// 			COUNT(*) > 1
	// 		ORDER BY
	// 			year,
	// 			month"
	// 	);
		
	// 	return 111;
	// }

	// public static function push_page_content($page_id, $content) {

	// 	$conn = self::get_connect('content');
	// 	$stmt = $conn->prepare(
	// 		"INSERT INTO `content` (`id`, `content`) VALUES (?, ?);"
	// 	);

	// 	$stmt->bind_param('is', $page_id, $content);
	// 	$result = self::execute($conn, $stmt, 'set');

	// 	return $result;
	// }

	// public static function update_page_content($page_id, $content) {

	// 	$conn = self::get_connect('content');
	// 	$stmt = $conn->prepare(
	// 		"UPDATE `content` SET `content` = ? WHERE `content`.`id` = ?"
	// 	);

	// 	$stmt->bind_param('si', $content, $page_id);
	// 	$result = self::execute($conn, $stmt, 'set');

	// 	return $result;
	// }

	// public static function push_or_update_page_content($page_id, $content) {

	// 	$conn = self::get_connect('content');
	// 	$stmt = $conn->prepare(
	// 		"INSERT INTO `content` (`id`, `content`)
	// 		VALUES (?, ?)
	// 		ON DUPLICATE KEY UPDATE content = ?;"
	// 	);

	// 	$stmt->bind_param('iss', $page_id, $content, $content);
	// 	$result = self::execute($conn, $stmt, 'set');

	// 	return $result;
	// }

}
