<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Answer.php';
require_once __DIR__ . '/classes/Logger.php';
require_once __DIR__ . '/classes/ExceptionHandler.php';

header(CTYPE);

if (isset($_GET['action'])){

	switch ($_GET['action']) {
		// case 'get_pages':
		// 	require_once('get_pages.php');
		// 	break;

		// case 'get_revs':
		// 	require_once('get_revs.php');
		// 	break;

		case 'search':
			require_once('search.php');
			break;

		default:
			Answer::fail('Incorrect action');
			break;
	}

} else {
	Answer::fail('Action is unset');
}
