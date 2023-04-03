<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/parser/Parser.php';

require_once __DIR__ . '/classes/Answer.php';
require_once __DIR__ . '/classes/Logger.php';
require_once __DIR__ . '/classes/MWQuery.php';
require_once __DIR__ . '/classes/ExceptionHandler.php';

Parser::update_revs();


// $test = MWQuery::get_revs_content_by_list([1000]);

// print_r($test)

// print_r($test);