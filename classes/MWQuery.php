<?php

class MWQuery {

	protected static function implode_list($list) {
		return implode('|', $list);
	}

	protected static function get_id_range($start, $length) {
		$list = [];

		for ($i = 0; $i < $length; $i++)
			$list[] = $start + $i;

		return self::implode_list($list);
	}

	protected static function get_rev_data($range) {
		$base_params = [
			'action' => 'query',
			'prop' => 'revisions',
			'rvprop' => 'ids|size|content',
			'formatversion'=> '2',
			'format' => 'json',
			'rvslots' => 'main',
		];

		$params = $base_params;
		$params['revids'] = $range;

		$query_params = http_build_query($params);
		$url = WIKI_API . '?' . $query_params;
	
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
	
		$result = json_decode($output, true);
	
		return $result['query'];
	}

	public static function get_revs_content_by_list(array $list) {
		$list_formated = self::implode_list($list);
		return self::get_rev_data($list_formated);
	}

	public static function get_revs_content_by_range(int $at, int $to) {
		$range = self::get_id_range($at, $to);
		return self::get_rev_data($range);
	}

}
