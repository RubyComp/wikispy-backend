<?php

require_once __DIR__ . '/../Query.php';
require_once __DIR__ . '/../MWQuery.php';
require_once __DIR__ . '/../search/LastRevId.php';

Class Parser {


	// public static function get_last_stored_rev_id() {
	// }

	public static function check_revs_update_requaired(array $status) {
		return $status > 0 ? true : false;
	}

	public static function get_revs_status() {
		$status = [];

		$status['stored'] = LastRevId::get_id();
		$status['actual'] = MWQuery::get_lastrevid();
		$status['dif'] = $status['actual'] - $status['stored'];

		return $status;
	}

	protected static function process_rev(array $data) {

		$result = [
			'revid' => $data['revid']
		];

		$is_missing = key_exists('missing', $data);

		if ($is_missing) {
			$result['missing'] = true;
		} else {
			$result['parentid'] = $data['parentid'];
			$result['size'] = $data['size'];
		}
		// print_r($result);
		// die();

		return $result;
	}

	protected static function process_revs_list(array $list) {
		$sorted_revs = self::sort_revs_list($list);
		foreach ($sorted_revs as $rev) {
			self::process_rev($rev);
		}
		return true;
	}

	protected static function get_revs_from_page(array $page_data) {
		return $page_data['revisions'];
	}

	protected static function sort_revs_list(array $list) {
		usort($list, function($a, $b) {
			return $a['revid'] - $b['revid'];
		});
		return $list;
	}

	protected static function parse_revs_chunk(int $at, int $count) {
		// print_r('parse: ' . $at . '-' . $at + $count . "\n");
		$query = MWQuery::get_revs_content_by_range($at, $count, true);
		$query = MWQuery::get_revs_content_by_range(1000, 1, false);
		
		// print_r($query);
		// die();
		$revs = [];

		if (key_exists('badrevids', $query))
			$revs = array_merge($revs, $query['badrevids']);

		if (key_exists('pages', $query)) {
			foreach ($query['pages'] as $page) {
				$page_revs = self::get_revs_from_page($page);
				$revs = array_merge($revs, $page_revs);
			}
		}

		self::process_revs_list($revs);

		return true;
	}

	public static function update_revs() {
		print_r('update_revs init'. "\n");

		$refs_status = self::get_revs_status();
		$refs_is_outdated = self::check_revs_update_requaired($refs_status);

		if (!$refs_is_outdated) {
			print_r('revs is up to date'. "\n");
			return;
		}

		print_r('need to update: '. $refs_status['dif'] . "\n");

		//

		$step = 25;
		$i = $refs_status['stored'] + 1;

		$actual = $refs_status['actual'];

		$test_count = 0;

		while ($i <= $actual) {
			if ($test_count > 3) die('die');
			$test_count++;
			$end = $i + $step;
			$last = $end > $actual ? $actual : $end;
			$len = $last - $i;
			// print_r($i . '-' . $last . ' / ' . $len . "\n");
			self::parse_revs_chunk($i, $len);
			sleep(1);
			$i += $step;
		}

		return;
	}

}
// $last_rev = MWQuery::get_revs_content_by_range($last_stored_revid, 25);