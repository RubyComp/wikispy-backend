<?php

require 'core/api/classes/Query.php';
require 'core/api/classes/Answer.php';

if (!isset($_GET['at']) || !isset($_GET['to'])) {
	Answer::fail('Params "at" and "to" is unset');
}

function valid_id($input) {
	if (is_int($input) && $input > 0)
		return htmlspecialchars($input);
	else
		return false;
}

$at = valid_id($_GET['at']);
$to = valid_id($_GET['to']);

if (!$at || !$to)
	Answer::fail('Incorrect "at" or "to" value');

$pages_list = Query::get_pages_by_id($at, $to);

Answer::success($pages_list);