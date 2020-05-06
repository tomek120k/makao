<?php


namespace Makao\Service;


use Exception;
use Makao\Collection\CardNotFoundException;
use Makao\Exception\GameException;
use Makao\Table;

class GameService
{

    const MINIMAL_PLAYERS = 2;
    const COUNT_START_PLAYER_CARDS = 5;
    private Table $table;
    /**
     * @var bool
     */
    private bool $isStarted = false;
    /**
     * @var CardService
     */
    private CardService $cardService;

    /**
     * GameService constructor.
     * @param Table|null $table
     * @param CardService $cardService
     */
    public function __construct(Table $table, CardService $cardService)
    {
        $this->table = $table;
        $this->cardService = $cardService;
    }

    public function isStarted(): bool
    {
        return $this->isStarted;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function addPlayers(array $players): self
    {
        foreach ($players as $player) {
            $this->table->addPlayer($player);
        }
        return $this;
    }

    public function startGame(): void
    {
        $this->validateBeforeStartGame();

        $cardDeck = $this->table->getCardDeck();
        try {
            $this->isStarted = true;
            $card = $this->cardService->pickFirstNoActionCard($cardDeck);

            $this->table->addPlayedCard($card);

            foreach ($this->table->getPlayers() as $player) {
                $player->takeCards($cardDeck, self::COUNT_START_PLAYER_CARDS);
            }
        } catch (Exception $e) {
            throw new GameException('The game needs help!', $e);
        }

    }

    public function prepareCardDeck()
    {
        $cardCollection = $this->cardService->createDeck();
        $cardDeck = $this->cardService->shuffle($cardCollection);
        return $this->table->addCardCollectionToDeck($cardDeck);
    }

    private function validateBeforeStartGame(): void
    {
        if (0 === $this->table->getCardDeck()->count()) {
            throw new GameException('Prepare card deck before game start');
        }

        if (self::MINIMAL_PLAYERS > $this->table->countPlayers()) {
            throw new GameException('You need minimum ' . self::MINIMAL_PLAYERS . ' players to start game');
        }
    }
}