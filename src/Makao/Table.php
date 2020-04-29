<?php


namespace Makao;


use Makao\Collection\CardCollection;
use Makao\Exception\TooManyPlayersAtTheTableException;

class Table
{
    const MAX_PLAYERS = 4;
    private $players = [];

    private $cardDeck;
    private $playedCards = null;
    private $currentIndexPlayer = 0;

    public function __construct(CardCollection $cardDeck = null, CardCollection $playedCards = null)
    {
        $this->cardDeck = $cardDeck ?? new CardCollection();
        $this->playedCards = new CardCollection();
    }

    public function addPlayer(Player $player): void
    {
        if (count($this->players) === self::MAX_PLAYERS) {
            throw new TooManyPlayersAtTheTableException(self::MAX_PLAYERS);
        }
        $this->players[] = $player;
    }

    public function getPlayedCards(): CardCollection
    {
        return $this->playedCards;
    }

    public function getCardDeck(): CardCollection
    {
        return $this->cardDeck;
    }

    public function addCardCollectionToDeck(CardCollection $cardCollection): self
    {
        $this->cardDeck->addCollection($cardCollection);
        return $this;
    }

    public function getCurrentPlayer(): Player
    {
        return $this->players[$this->currentIndexPlayer];
    }

    public function getNextPlayer(): Player
    {
        return $this->players[$this->currentIndexPlayer + 1] ?? $this->players[0];
    }

    public function getPreviousPlayer(): Player
    {
        return $this->players[$this->currentIndexPlayer - 1] ?? $this->players[$this->countPlayers() - 1];
    }

    public function countPlayers(): int
    {
        return count($this->players);
    }

    public function finishRound(): void
    {
        if (++$this->currentIndexPlayer === $this->countPlayers()) {
            $this->currentIndexPlayer = 0;
        }
    }

    public function backRound()
    {
        if (--$this->currentIndexPlayer < 0) {
            $this->currentIndexPlayer = $this->countPlayers() - 1;
        }
    }
}