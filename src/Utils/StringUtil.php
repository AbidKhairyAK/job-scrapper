<?php

namespace App\Utils;

class StringUtil
{
	public static function sanitizeString(string $str)
	{
		$nbsp = str_replace('&nbsp;', ' ', $str);
		$amp = str_replace('&amp;', '&', $nbsp);
		$quote = str_replace('"', '', $amp);
		return str_replace([',', ';'], '.', $quote);
	}
}