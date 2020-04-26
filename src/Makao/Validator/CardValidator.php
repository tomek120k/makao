<?php


namespace Makao\Validator;


use Makao\Card;
use Makao\Exception\CardDuplicationException;

class CardValidator
{
    public function valid(Card $activeCard, Card $newCard): bool
    {
        if ($activeCard === $newCard) {
            throw new CardDuplicationException($newCard);
        }
        return $activeCard->getColor() === $newCard->getColor() || $activeCard->getValue() === $newCard->getValue();
    }
}