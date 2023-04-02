<?php

require_once __DIR__ . '/classes/parser/Parser.php';
require_once __DIR__ . '/classes/MWQuery.php';

// print_r(Parser::get_lastrevid());
print_r(Parser::test());
print_r("\n");
print_r(MWQuery::get_lastrevid());