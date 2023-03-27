<?php
require __DIR__ . '/Search.php';
require __DIR__ . '/ResultsCount.php';

class ResultsList extends Search {

	protected static $params = [
		'text'    => 'string',
		'order'   => 'string',
		'types'   => 'string',
		'page'    => 'int',
		'limit'   => 'int',
		'nspaces' => 'array',
	];

	public static function get_params() {
		return self::$params;
	}

	protected static function get_sql(string $page, string $content, string $order) {
		$sql = "SELECT
			`$content`.id,
			`$page`.title,
			`$page`.ns,
			`$page`.issub,
			`$page`.lastrev

			FROM `$content`
			INNER JOIN `$page` ON `$content`.id = `$page`.id
			WHERE content LIKE ?
			AND `$content`.`ns` IN (?)
			AND `$content`.`issub` IN (?)
			ORDER BY `$page`.`$order`
			LIMIT ? OFFSET ?;";

		return $sql;
	}

	protected static function parse_param(array $data, string $name, string $type) {

		if (Validate::arr_has_value($data, $name))
			$value = htmlspecialchars($data[$name]);
		else
			$value = false;

		switch ($type) {
			case 'string':
				return $value ? $value : '';

			case 'array':
				return Validate::string_to_arr($value);

			case 'int':
				return Validate::string_to_int($value);

			default:
				return '';
		}
	}

	protected static function set_param(array $data, string $name, string $type) {

		$value = $data[$name];

		switch ($name) {
			case 'text':
				$valid = Validate::get_text($value);
				break;

			case 'page':
			case 'limit':
				$valid = Validate::get_plus_int($value);
				break;

			case 'nspaces':
				$valid = Validate::get_int_arr($value);
				break;

			case 'order':
				$valid = Validate::get_white_listed($value, self::get_orders_list());
				break;

			case 'types':
				$valid = Validate::get_white_listed($value, self::get_types_list());
				break;

			default:
				ExceptionHandler::show_error('Incrorrect param name: ' . $name);
				break;
		}

		return ($valid !== false) ? $valid : self::get_default_val($name);

	}

	protected static function prepare(array $data) {
		$result = [];
		foreach (self::$params as $name => $type) {
			$parsed[$name] = self::parse_param($data, $name, $type);
			$result[$name] = self::set_param($parsed, $name, $type);
		}
		return $result;
	}

	protected static function get_offset(array $params) {
		if (Validate::array_has_keys($params, ['limit', 'page']) && +$params['limit'] > 0 && +$params['page'] > 0)
			return $params['limit'] * ($params['page'] - 1);
		else
			ExceptionHandler::show_error('Offset generate error');
	}

	public static function get_list(array $params){

		$prepared = self::prepare($params);
		$prepared['types'] = self::get_types_arr($prepared);
		$prepared['limit'] = self::validate_limit($prepared);
		$prepared['offset'] = self::get_offset($prepared);

		$query = Validate::stringify_array($prepared);
		$query['text'] = self::get_search_action($prepared['text'], 'like');

		$table_pages   = self::$config['pages-table'];
		$table_content = self::$config['content-table'];

		$conn = self::get_connect('content');
		$sql = self::get_sql($table_pages, $table_content, $query['order']);

		$stmt = self::prepare_query(
			$conn,
			$sql,
			[
				'sssii',
				$query['text'],
				$query['nspaces'],
				$query['types'],
				$query['limit'],
				$query['offset']
			]
		);

		$count = ResultsCount::get_count([
			'table_content' => $table_content,
			'table_pages'    => $table_pages,
			'text'   => $query['text'],
			'nspace' => $query['nspaces'],
			'types'  => $query['types'],
		]);

		$result_list = self::execute($conn, $stmt, 'get');

		return [
			'namespaces'  => $prepared['nspaces'],
			'count'       => $count,
			'text'        => $prepared['text'],
			'types' => [
				'main'     => in_array(0, $prepared['types']),
				'subpages' => in_array(1, $prepared['types'])
			],
			'order' => [
				'value'    => $query['order'],
				'possible' => self::get_orders_list(),
			],
			'paginator'   => [
				'page'     => $prepared['page'],
				'limit'    => $prepared['limit'],
				'offset'   => $prepared['offset'],
				'total'    => ceil($count / $prepared['limit'])
			],
			'result'      => $result_list
		];
	}

}
