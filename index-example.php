<?php

require_once 'vendor/autoload.php';

use App\Spiders\ImdbTopMoviesSpider;
use App\Spiders\OpenLibrarySpider;
use App\Utils\PostAction;
use RoachPHP\Roach;

// $topMovieDetails = Roach::collectSpider(ImdbTopMoviesSpider::class);
$trendingBooks = Roach::collectSpider(OpenLibrarySpider::class);

// dump($trendingBooks);
$trendingBooks = PostAction::convertItemsToJson($trendingBooks);
PostAction::saveJsonAsCSV($trendingBooks, './output/test2.csv');