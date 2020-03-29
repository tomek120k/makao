<?php


namespace Makao\Service;


/**
 * Class ShuffleService
 * @package Makao\Service
 * @CodeCoveregeIgnore
 */
class ShuffleService
{
	public function shuffle (array $data)
	{
		shuffle($data);
		return $data;
	}
}