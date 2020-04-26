<?php


namespace Makao\Exception;


use RuntimeException;
use Throwable;

class TooManyPlayersAtTheTableException extends RuntimeException
{
    public function __construct($maxPlayers, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Max capacity is %s players!', $maxPlayers);
        parent::__construct($message, $code, $previous);
    }
}