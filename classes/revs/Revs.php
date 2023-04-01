<?php

require_once __DIR__ . '../../Query.php';
require_once __DIR__ . '../../ChartData.php';

class Revs extends Query {

	protected static $modes = ['all', 'bots', 'nobots'];

	protected static $revs_sql = [

		'all' => "SELECT
			YEAR(timestamp) AS year,
			MONTH(timestamp) AS month,
			COUNT(*) AS edits,
			COUNT(DISTINCT userid) AS users
		FROM
			`revs` ft
		WHERE
			ft.missing = 0
		GROUP BY
			YEAR(timestamp),
			MONTH(timestamp)
		HAVING
			COUNT(*) > 1
		ORDER BY
			year,
			month",

		'bots' => "SELECT
				YEAR(timestamp) AS year,
				MONTH(timestamp) AS month,
				COUNT(*) AS edits,
				COUNT(DISTINCT userid) AS users
			FROM
				`revs` ft
				JOIN `users` ut ON ft.userid = ut.id
			WHERE
				ft.missing = 0 AND ut.bot = true
			GROUP BY
				YEAR(timestamp),
				MONTH(timestamp)
			HAVING
				COUNT(*) > 1
			ORDER BY
				year,
				month",

		'nobots' => "SELECT
				YEAR(timestamp) AS year,
				MONTH(timestamp) AS month,
				COUNT(*) AS edits,
				COUNT(DISTINCT userid) AS users
			FROM
				`revs` ft
				JOIN `users` ut ON ft.userid = ut.id
			WHERE
				ft.missing = 0
				AND (ut.bot IS NULL OR ut.bot <> true)
			GROUP BY
				YEAR(timestamp),
				MONTH(timestamp)
			HAVING
				COUNT(*) > 1
			ORDER BY
				year,
				month",

	];

	protected static function validate_mode(string $value) {
		if (!in_array($value, self::$modes))
			return self::$modes[0];
		else
			return $value;
	}

	protected static function get_sql(string $mode) {
		return self::$revs_sql[$mode];
	}

	public static function get_stats(string $mode) {

		$valid_mode = self::validate_mode($mode);
		
		$conn = self::get_connect('revs');
		$sql = self::get_sql($valid_mode);

		$stmt = self::prepare_query(
			$conn,
			$sql,
			[]
		);

		$result = self::execute($conn, $stmt, 'get');

		return [
			ChartData::format($result['data']),
		];

	}

}
