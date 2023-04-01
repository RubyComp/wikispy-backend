<?php

/*
* Make specific data format for chart js
*/

Class ChartData {

	protected static $values = [
		'edits',
		'users'
	];

	protected static function get_blank() {
		$blank = [
			'labels' => [/*'10;2015','11;2015'*/],
			'datasets' => [
				// [
				// 	'label' => 'edits',
				// 	'data' => [/*4000, 4800*/],
				// ],
				// [
				// 	'label' => 'users',
				// 	'data' => [/*10, 20*/],
				// ],
			],
		];
		return $blank;
	}

	protected static function fill_year() {
		return array_fill_keys(range(1,12), []);
	}

	protected static function pute_raw_data_by_mounth(array $data) {
		$result = [];
		foreach (self::$values as $key => $value) {
			// $result[$value] = intval($data[$value]);
			array_push($result, intval($data[$value]));
		}
		return $result;
	}

	protected static function prepere_data_by_mounths(array $data) {

		$result = [];

		foreach ($data as $key => $value) {
			$year = $value['year'];
			$month = $value['month'];

			if(!key_exists($year, $result))
				$result[$year] = self::fill_year();

			$result[$year][$month] = self::pute_raw_data_by_mounth($value);
		}
		return $result;
	}

	protected static function trim_first_year(array $data, int $first_year) {

		foreach ($data[$first_year] as $mounth => $val) {
			if (empty($val))
				unset($data[$first_year][$mounth]);
			else
				break;
		}
		
		return $data;
	}

	protected static function trim_last_year(array $data, int $last_year) {

		foreach (array_reverse($data[$last_year]) as $mounth => $val) {
			if (empty($val) || $mounth == abs(date('n')-12))
				unset($data[$last_year][abs($mounth-12)]);
			else
				break;
		}
		
		return $data;
	}

	protected static function trim_years(array $data) {

		$years_keys = array_keys($data);

		$first_year = min($years_keys);
		$last_year = max($years_keys);

		$data = self::trim_first_year($data, $first_year);
		$data = self::trim_last_year($data, $last_year);

		return $data;
	}

	protected static function separate(array $data) {

		$result = [];

		foreach ($data as $year => $months) {
			foreach ($months as $month => $values) {
				
				foreach (self::$values as $key => $val_name) {
					if (!empty($values))
						$new_val = $values[$key];
					else
						$new_val = 0;

					$result[$key][$year][$month] = $new_val;
				}
			}
		}
		return $result;
	}

	protected static function flatten_array($array) {
		$result = [];
		foreach ($array as $value) {
			if (is_array($value))
				$result = array_merge($result, self::flatten_array($value));
			else
				$result[] = $value;
		}
		return $result;
	}

	public static function format(array $input) {
		$prepered = self::prepere_data_by_mounths($input);
		$trimed = self::trim_years($prepered);
		$separated = self::separate($trimed);

		$data = self::get_blank();

		foreach ($trimed as $year => $mounths) {
			foreach ($mounths as $mounth => $value) {
				array_push($data['labels'], $mounth . ';' . $year);
				// array_push($data['datasets'][0]['data'], $value);
			}
		}

		foreach ($separated as $key => $value) {
			$data['datasets'][$key] = [
				'label' => self::$values[$key],
				'data' => self::flatten_array($value),
				'yAxisID' => self::$values[$key],
			];
		}

		return $data;
	}

}