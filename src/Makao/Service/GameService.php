<?php


namespace Makao\Service;


use Makao\Table;

class GameService
{

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
        $this->isStarted = true;
    }

    public function prepareCardDeck()
    {
        $cardCollection = $this->cardService->createDeck();
        $cardDeck = $this->cardService->shuffle($cardCollection);
        return $this->table->addCardCollectionToDeck($cardDeck);
    }
}