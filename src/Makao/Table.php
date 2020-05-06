<?php


namespace Makao;


use Makao\Collection\CardCollection;
use Makao\Collection\CardNotFoundException;
use Makao\Exception\TooManyPlayersAtTheTableException;

class Table
{
    const MAX_PLAYERS = 4;
    private $players = [];

    private $cardDeck;
    private $playedCards = null;
    private $currentIndexPlayer = 0;
    private string $playedCardColor = '';

    public function __construct(CardCollection $cardDeck = null, CardCollection $playedCards = null)
    {
        $this->cardDeck = $cardDeck ?? new CardCollection();
        $this->playedCards = $playedCards ?? new CardCollection();

        if (!is_null($playedCards)) {
            $this->changePlayedCardColor($this->playedCards->getLastCard()->getColor());
        }
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

    public function getPlayedCardColor() : string
    {
        if ($this->playedCardColor) {

            return $this->playedCardColor;
        }


        throw new CardNotFoundException('No played card on the table yet!');
    }

    public function addPlayedCard(Card $card) : self
    {
        $this->playedCards->add($card);
        $this->changePlayedCardColor($card->getColor());
        return $this;
    }

    public function changePlayedCardColor($color) : self
    {
        $this->playedCardColor = $color;
        return $this;
    }

    public function addPlayedCards(CardCollection $cards) : self
    {
        foreach ($cards as $card) {
            $this->addPlayedCard($card);
        }
        return $this;
    }

    /**
     * @return Player[]
     */
    public function getPlayers () : array
    {
        return $this->players;
    }
}