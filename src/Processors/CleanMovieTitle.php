<?php

namespace App\Processors;

use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;

class CleanMovieTitle implements ItemProcessorInterface
{
	public function configure (array $options): void
	{}

	public function processItem(ItemInterface $item): ItemInterface
	{
		$item->set('title', preg_replace('/^\d+\.\s/', '', $item->get('title')));
		return $item;
	}
}