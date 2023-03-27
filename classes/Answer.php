<?php

class Answer {

	private static function get_result($is_fine, $data, $message) {
		return [
			'status' => $is_fine ? 'ok' : 'fail',
			'data' => $data,
			'message' => $message,
		];
	}

	private static function format($data) {
		return json_encode($data);
	}

	private static function show($is_fine, $content, $comment) {
		$result = self::get_result($is_fine, $content, $comment);
		$json = self::format($result);
		print($json);
	}

	public static function success(array $content = [], string $comment = '') {
		self::show(true, $content, $comment);
	}

	public static function fail(string $comment = '') {
		self::show(false, [], $comment);
		die();
	}

}
