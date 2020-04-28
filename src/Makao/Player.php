<?php


namespace Makao;


use Makao\Collection\CardCollection;
use Makao\Collection\CardNotFoundException;

class Player
{
    const MAKAO = "Makao";
    private $name = '';
    private $cardCollection = null;
    private int $roundToSkip = 0;

    public function __construct(string $name = '', CardCollection $cardCollection = null)
    {
        $this->name = $name;
        $this->cardCollection = $cardCollection ?? new CardCollection();
    }

    public function takeCards(CardCollection $cardCollection, int $count = 1): self
    {
        for ($i = 0; $i < $count; $i++) {
            $this->cardCollection->add($cardCollection->pickCard());
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function sayMakao(): string
    {
        return self::MAKAO;
    }

    public function pickCardByValue(string $value): Card
    {
        foreach ($this->cardCollection as $index => $card) {
            if ($value === $card->getValue()) {
                return $this->pickCard($index);
            }
        }
        throw new CardNotFoundException('Player has not card with value 2');
    }

    public function pickCardsByValue(?string $cardValue): CardCollection
    {
        $cardCollection = new CardCollection();
        try {
            while ($card = $this->pickCardByValue($cardValue)) {
                $cardCollection->add($card);
            }
        } catch (CardNotFoundException $e) {
            if (0 === $cardCollection->count()) {
                throw $e;
            }
        }
        return $cardCollection;
    }

    public function pickCard($index = 0): Card
    {
        return $this->getCards()->pickCard($index);
    }

    public function getCards(): CardCollection
    {
        return $this->cardCollection;
    }

    public function canPlayRound(): bool
    {
        return $this->getRoundToSkip() === 0;
    }

    public function getRoundToSkip(): int
    {
        return $this->roundToSkip;
    }

    public function addRoundToSkip(int $round = 1): self
    {
        $this->roundToSkip += $round;
        return $this;
    }

    public function skipRound(): self
    {
        --$this->roundToSkip;
        return $this;
    }
}