<?php

class Logger {

	public static $file_name = 'log.txt';

	public static function get_file_path() {
		return __DIR__ . '/../../../' . self::$file_name;
	}

	protected static function file_exists() {
		return file_exists(self::get_file_path());
	}

	protected static function create_file_blank() {
		$handle = fopen(self::get_file_path(), 'w') or die('File create failed.');
		fclose($handle);
	}

	protected static function get_date() {
		return date('Y-m-d H:i:s');
	}

	public static function write($text) {

		$file_path = self::get_file_path();
		$current = file_get_contents($file_path);

		$file = fopen($file_path, 'w')
			or self::tg_log('Unable to open file ' . $file_path . "\n" . $text);
			// die('Unable to open file ' . $file_path);

		file_put_contents($file_path, $current . $text . "\n");
		fclose($file);
	}

	public static function log(string $text) {

		if (!self::file_exists())
			self::create_file_blank();

		$date = self::get_date();
		$ip = $_SERVER['REMOTE_ADDR'];

		self::write("{$date} - {$ip} - {$text}");
		self::tg_log("{$ip} - {$text}");

	}

	protected static function tg_log($text) {

		$encodedMessage = urlencode($text);

		$queryUrl = 'https://api.telegram.org/bot' . TG_TOKEN . '/sendMessage?chat_id=' . TG_USER_ID . '&text=' . $encodedMessage;

		file_get_contents($queryUrl);
	}


}