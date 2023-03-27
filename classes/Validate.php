<?php

class Validate {

	public static function arr_has_value(array $arr, string $val) {
		return (isset($arr[$val]) && strlen($arr[$val]) > 0);
	}

	public static function string_to_int(string|int|bool $input) {
		if ($input && is_int(+$input) && +$input >= 0)
			return +htmlspecialchars($input);
		else
			return 0;
	}

	public static function string_to_arr(string|bool $input) {
		$result = [];

		if ($input === false)
			return $result;

		$raw_string = htmlspecialchars($input);
		$raw_list = explode(',', $raw_string);
	
		foreach ($raw_list as $key => $item) {
			$clear = self::string_to_int(+$item);

			if (!$clear && $clear !== 0)
				ExceptionHandler::show_error('Incrorrect array value in "string_to_arr" for ' . $raw_string);
			else
				array_push($result, $clear);
		}
		return $result;
	}

	public static function arr_to_string(array $item) {
		return implode(',', $item);
	}

	public static function get_plus_int(string|int $value) {
		if ($value || +$value > 0)
			return +$value;
		else
			return false;
	}

	
	public static function get_text(string $text) {
		if (strlen($text))
			return str_replace('_', ' ', htmlspecialchars($text));
		else
			return false;
	}

	public static function get_white_listed(string $value, array $white) {
		if (in_array($value, $white))
			return $value;
		else
			return false;
	}

	public static function get_int_arr(array $arr) {
		if (count($arr)) {
			foreach ($arr as $key => $value)
				if (!is_int($key)) ExceptionHandler::show_error('Incorrect value (int)');
			return $arr;
		} else {
			return false;
		}
	}

	public static function array_has_keys(array $arr, array $keys) {
		$result = true;
		foreach ($keys as $name) {
			if (!array_key_exists($name, $arr))
				$result = false;
		}
		return $result;
	}

	public static function stringify_array(array $arr) {
		return array_map(function($item) {
			if (is_array($item))
				return Validate::arr_to_string($item);
			else
				return $item;
		}, $arr);
	}

}
