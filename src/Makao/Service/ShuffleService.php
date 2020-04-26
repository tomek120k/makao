<?php


namespace Makao\Service;


/**
 * Class ShuffleService
 * @package Makao\Service
 * @CodeCoverageIgnore
 */
class ShuffleService
{
    /**
     * @param array $data
     * @return array
     * @CodeCoverageIgnore
     */
    public function shuffle(array $data): array
    {
        shuffle($data);
        return $data;
    }
}