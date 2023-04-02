<?php

Class Parser {

	public static function get_lastrevid() {

		$url = WIKI_API . '?action=query&list=recentchanges&rclimit=1&rcprop=ids&format=json';
		$json = file_get_contents($url);
		$answer = json_decode($json);

		$lastrevid = $answer->query->recentchanges[0]->old_revid;

		return $lastrevid;

	}

}