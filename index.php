<?php

require_once 'vendor/autoload.php';

use App\Spiders\JobstreetSpider;
use App\Utils\PostAction;
use RoachPHP\Roach;

$scrappingResults = Roach::collectSpider(JobstreetSpider::class);

// dump($scrappingResults);

$resultsJson 	= PostAction::convertItemsToJson($scrappingResults);
$filename 		= PostAction::createFilenameCSV('Jobstreet');

PostAction::saveJsonAsCSV($resultsJson, $filename);