<?php

require_once __DIR__ . '../../Query.php';

class Revs extends Query {

	protected static $modes = ['all', 'bots', 'nobots'];

	protected static $revs_sql = [

		'all' => "SELECT
			YEAR(timestamp) AS year,
			MONTH(timestamp) AS month,
			COUNT(*) AS count
		FROM
			`revs` ft
			JOIN `users` ut ON ft.userid = ut.id
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
				COUNT(*) AS count
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
				COUNT(*) AS count
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

	/*
	* Make specific date format for chart js
	*/
	protected static function format_data(array $input) {

		$data = [
			'labels' => [/*'10;2015','11;2015'*/],
			'datasets' => [
				[
					'label' => 'edits',
					'data' => [/*4000, 4800*/],
				],
			],
		];

		$raw_data = [];

		foreach ($input as $key => $value) {
			$year = $value['year'];

			if(!key_exists($year, $raw_data))
				$raw_data[$year] = array_fill_keys(range(1,12), 0);

			$raw_data[$year][$value['month']] = intval($value['count']);
		}

		$years_keys = array_keys($raw_data);

		$first_year = min($years_keys);
		$last_year = max($years_keys);

		foreach ($raw_data[$first_year] as $mounth => $edits) {
			if ($edits == 0)
				unset($raw_data[$first_year][$mounth]);
			else
				break;
		}

		foreach (array_reverse($raw_data[$last_year]) as $mounth => $edits) {
			if ($edits == 0 || $mounth == abs(date('n')-12))
				unset($raw_data[$last_year][abs($mounth-12)]);
			else
				break;
		}

		foreach ($raw_data as $year => $mounths) {
			foreach ($mounths as $mounth => $edits) {
				array_push($data['labels'], $mounth . ';' . $year);
				array_push($data['datasets'][0]['data'], $edits);
			}
		}

		return $data;
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

		return self::format_data($result['data']);

	}

}
