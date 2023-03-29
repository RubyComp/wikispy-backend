<?php

require_once __DIR__ . '../../Query.php';

class Search extends Query {

	protected static $default = [
		'limit' => 25,
		'nspaces' => [0],
		'order' => 'id',
		'direct' => 'desc',
		'page' => 1,
		'types' => 'all',
	];

	protected static $orders_list = [
		'id',
		'title',
		'nspaces',
		'lastrev',
	];

	protected static $types_list = [
		'all',
		'master',
		'sub'
	];

	protected static $direct_list = [
		'asc',
		'desc'
	];

	public static function get_default_val(string $key) {
		if (array_key_exists($key, self::$default))
			return self::$default[$key];
		else
			ExceptionHandler::show_error('Have no default value for: ' . $key);
	}

	public static function get_orders_list() {
		return self::$orders_list;
	}

	public static function get_types_list() {
		return self::$types_list;
	}

	public static function get_direct_list() {
		return self::$direct_list;
	}

	public static function get_search_action(string $text, string $mode) {
		switch ($mode) {
			case 'like':
				return "%{$text}%";
			default:
				return $text;
		}
	}

	public static function get_types_arr(array $params) {
		$all = [0,1];
		if (Validate::arr_has_value($params, 'types'))
			$mode = $params['types'];
		else
			return $all;

		switch ($mode) {
			case 'all':
				return $all;
			case 'master':
				return [0];
			case 'sub':
				return [1];
			default:
				return $all;
		}
	}

	protected static function prepare_query($conn, $sql, $data) {
		$stmt = $conn->prepare($sql);

		if(!$stmt)
			ExceptionHandler::show_error("SQL Error: {$conn->errno} - {$conn->error}");

		$stmt->bind_param(...$data);
		return $stmt;
	}

	protected static function validate_limit(array $params) {
		$default = self::$default['limit'];
		if (Validate::arr_has_value($params, 'limit')) {
			if (+$params['limit'] <= self::$config['max-show'])
				return +$params['limit'];
		}
		return $default;

	}

}
