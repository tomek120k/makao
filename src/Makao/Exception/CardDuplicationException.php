<?php


namespace Makao\Exception;


use Exception;
use Makao\Card;
use Throwable;

class CardDuplicationException extends Exception
{
    public function __construct(Card $card, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Valid cards are the same cards: %s %s', $card->getValue(), $card->getColor());
        parent::__construct($message, $code, $previous);
    }
}