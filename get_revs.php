<?php
require_once __DIR__ . '/classes/revs/Revs.php';
require_once __DIR__ . '/classes/Validate.php';

if (!Validate::arr_has_value($_GET, 'mode'))
	Answer::fail('Mode is unset');

Answer::success([
	Revs::get_stats(htmlspecialchars($_GET['mode']))
]);
