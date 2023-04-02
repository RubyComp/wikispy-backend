<?php

require_once __DIR__ . '/../Query.php';
require_once __DIR__ . '/../MWQuery.php';
require_once __DIR__ . '/../search/LastRevId.php';

Class Parser {


	// public static function get_last_stored_rev_id() {
	// }

	public static function test() {
		return LastRevId::get_id();
	}



}