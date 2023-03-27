<?php

require __DIR__ . '/Logger.php';

class ExceptionHandler {

	public static function show_error(string $text) {

		Logger::log($text);

		if (DEBUG)
			print_r($text);

		die('');
	}

}
