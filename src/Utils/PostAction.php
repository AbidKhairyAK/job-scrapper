<?php

namespace App\Utils;

class PostAction
{
	public static function convertItemsToJson (array $items): string
	{
		$normalizedItems = array_map(fn ($item) => $item->all(), $items);
		$jsonItems = json_encode($normalizedItems, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

		return $jsonItems;
	}


	public static function createFilenameCSV (string $title) {
		$date 		= date('Y-m-d H:i:s');
		$format 	= 'csv';
		$output_dir	= './output';
		$filename 	= "$output_dir/$date - $title - Scrapped Jobs.$format";

		return $filename;
	}


	public static function saveJsonAsCSV (string $jsonString, string $filename): void
	{
		$jsonArray 	= json_decode($jsonString, true);
		$fp 		= fopen($filename, 'w');
		$header 	= false;

		foreach ($jsonArray as $line) {
			if (empty($header)) {
				$header = array_keys($line);
				fputcsv($fp, $header);
				$header = array_flip($header);
			}

			fputcsv($fp, array_merge($header, $line));
		}

		fclose($fp);
		return;
	}
}