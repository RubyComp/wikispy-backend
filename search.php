<?php

require __DIR__ . '/classes/search/ResultsList.php';
require __DIR__ . '/classes/Validate.php';

if (!Validate::arr_has_value($_GET, 'text'))
	Answer::fail('Params "text" is unset');

$values = [];

foreach (ResultsList::get_params() as $name => $type) {
	if (Validate::arr_has_value($_GET, $name)) {
		$values[$name] = htmlspecialchars($_GET[$name]);
	}
}

Answer::success([
	ResultsList::get_list($values)
]);