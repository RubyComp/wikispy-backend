<?php

class MWQuery {

	protected static function implode_list(array $list) {
		return implode('|', $list);
	}

	protected static function get_id_range(int $start, int $length) {
		$list = [];

		for ($i = 0; $i < $length; $i++)
			$list[] = $start + $i;

		return self::implode_list($list);
	}

protected static function get_rev_data(string $range, bool $with_content = false) {
	$base_params = [
		'action' => 'query',
		'prop' => 'revisions',
		'rvprop' => 'ids|size',
		'formatversion'=> '2',
		'format' => 'json',
		'rvslots' => 'main',
	];

	if ($with_content)
		$base_params['rvprop'] .= '|content';

	$params = $base_params;
	$params['revids'] = $range;

	$query_params = http_build_query($params);
	$url = WIKI_API . '?' . $query_params;
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);

	if(curl_error($ch))
		ExceptionHandler::show_error('Curl error: ' . curl_error($ch));

	$result = json_decode($output, true);

	if (!$result || !key_exists('query', $result))
		ExceptionHandler::show_error('Parser error. Have no query.');
	else
		return $result['query'];
}

	public static function get_revs_content_by_list(array $list) {
		$list_formated = self::implode_list($list);
		return self::get_rev_data($list_formated);
	}

	public static function get_revs_content_by_range(int $at, int $to, bool $with_content = false) {
		$range = self::get_id_range($at, $to);
		return self::get_rev_data($range, $with_content);
	}

	public static function get_lastrevid() {

		$url = WIKI_API . '?action=query&list=recentchanges&rclimit=1&rcprop=ids&format=json';
		$json = file_get_contents($url);
		$answer = json_decode($json);

		$lastrevid = $answer->query->recentchanges[0]->revid;

		return $lastrevid;

	}
}
