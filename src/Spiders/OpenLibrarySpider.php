<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use Symfony\Component\DomCrawler\Crawler;

class OpenLibrarySpider extends BasicSpider
{
	/**
	 * @var string[]
	 */
	public array $startUrls = [
		'https://openlibrary.org/trending/forever'
	];

	public array $downloaderMiddleware = [
		RequestDeduplicationMiddleware::class,
		[UserAgentMiddleware::class, ['userAgent' => 'Mozilla/5.0 (compatible; RoachPHP/0.1.0)']],
	];

	public function parse (Response $response): Generator
	{
		$items = $response
			->filter('ul.list-books > li')
			->each(fn (Crawler $node) => [
				'title'		=> $node->filter('.resultTitle a')->text(),
				'url'		=> $node->filter('.resultTitle a')->link()->getUri(),
				'author'	=> $node->filter('.bookauthor a')->text(),
				'cover'		=> $node->filter('.bookcover img')->attr('src'),
			]);

		foreach ($items as $item) {
			// yield $this->item($item);
			yield $this->request(
				'GET',
				$item['url'],
				'parseBookPage',
				['item' => $item]
			);
		}

		// try {
		// 	$nexPageUrl = $response->filter('div.pager div.pagination > :last-child')->link()->getUri();
		// 	yield $this->request('GET', $nexPageUrl);
		// } catch (\Exception) {
		// }
	}

	public function parseBookPage (Response $response)
	{
		$item = $response->getRequest()->getOptions()['item'];

		$descriptionArray = $response
			->filter('div.read-more__content p')
			->each(fn (Crawler $node) => $node->text());

		$item['description'] 	= implode('\n', $descriptionArray);
		$item['pages'] 			= $response->filter('span[itemprop="numberOfPages"]')->innerText();
		$item['publishDate']	= $response->filter('span[itemprop="datePublished"]')->innerText();

		yield $this->item($item);
	}
}