<?php
require 'config.php';
require 'queries.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['mode'])) {
	fail('Mode is unset');
}

if (!array_key_exists($_GET['mode'], $queries['edits'])) {
	fail('Mode is uncurrect');
} else {
	$sql = $queries['edits'][$_GET['mode']];
}

$conn = new mysqli($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME);

if ($conn->connect_error) {
	fail('Data base connection failed: ' . $conn->connect_error);
}

$conn->set_charset($CHARSET);
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	$raw_data = [];

	$data = [
		'labels' => [/*'10;2015','11;2015'*/],
		'datasets' => [
			[
				'label' => 'edits',
				'data' => [/*4000, 4800*/],
			],
		],
	];

	while($row = $result->fetch_assoc()) {

		$year = $row['year'];

		if(!key_exists($year, $raw_data))
			$raw_data[$year] = array_fill_keys(range(1,12), 0);

		$raw_data[$year][$row['month']] = intval($row['count']);

	}

	$years_keys = array_keys($raw_data);

	$first_year = min($years_keys);
	$last_year = max($years_keys);

	foreach ($raw_data[$first_year] as $mounth => $edits) {
		if ($edits == 0)
			unset($raw_data[$first_year][$mounth]);
		else
			break;
	}

	foreach (array_reverse($raw_data[$last_year]) as $mounth => $edits) {
		if ($edits == 0 || $mounth == abs(date('n')-12))
			unset($raw_data[$last_year][abs($mounth-12)]);
		else
			break;
	}

	foreach ($raw_data as $year => $mounths) {
		foreach ($mounths as $mounth => $edits) {
			array_push($data['labels'], $mounth . ';' . $year);
			array_push($data['datasets'][0]['data'], $edits);
		}
	}

	answer(true, $data);

} else {
	answer(true, [], 'No results');
}

$conn->close();