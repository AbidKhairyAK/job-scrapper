<?php

namespace App\Spiders;

use App\Utils\StringUtil;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use Symfony\Component\DomCrawler\Crawler;

class JobstreetSpider extends BasicSpider
{
	public array $startUrls = [
		'https://www.jobstreet.co.id/id/jobs-in-information-communication-technology?sortmode=ListedDate'
	];

	public array $downloaderMiddleware = [
		RequestDeduplicationMiddleware::class,
		[UserAgentMiddleware::class, ['userAgent' => 'Mozilla/5.0 (compatible; RoachPHP/0.1.0)']]
	];

	public function parse (Response $response): Generator
	{
		try {
			$items = $response
				->filter('div[data-search-sol-meta]')
				->each(function (Crawler $node) {

					$title 			= $node->filter('[data-automation=jobTitle]')->innerText();
					$category	 	= $node->filter('[data-automation=jobSubClassification]')->innerText();
					$company 		= $node->filter('[data-automation=jobCompany]')->innerText();
					$location 		= $node->filter('[data-automation=jobLocation]')->innerText();
					$salary 		= $node->filter('[data-automation=jobSalary] span')->innerText();
					$listing_time	= $node->filter('[data-automation=jobListingDate]')->innerText();
					$url 			= $node->filter('[data-automation=jobTitle]')->link()->getUri();
					$separatedQuery	= explode('?', $url);
					$separatedPath 	= explode('/', $separatedQuery[0]);
					$jobId 			= $separatedPath[count($separatedPath) - 1];

					return [
						'jobId' 		=> StringUtil::sanitizeString($jobId),
						'title' 		=> StringUtil::sanitizeString($title),
						'category'	 	=> StringUtil::sanitizeString($category),
						'company' 		=> StringUtil::sanitizeString($company),
						'location' 		=> StringUtil::sanitizeString($location),
						'salary' 		=> StringUtil::sanitizeString($salary),
						'listing_time' 	=> StringUtil::sanitizeString($listing_time),
						'url' 			=> $url,
					];
				});

			foreach ($items as $item) {
				yield $this->item($item);
			}

			// ===== stop on page 4 for testing purpose =====
			// $isPage5Clickable = $response->filter('a[data-automation=page-5]')->count() > 0;
			// if ($isPage5Clickable) {
			// 	throw new \Exception("stop here!");
			// }

			sleep(2);

			$nexPageUrl = $response->filter('a[title=Selanjutnya]')->link()->getUri();
			yield $this->request('GET', $nexPageUrl);

		} catch (\Exception) {}
	}
}