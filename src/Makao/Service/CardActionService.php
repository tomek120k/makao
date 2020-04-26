<?php


namespace Makao\Service;


use Makao\Card;
use Makao\Collection\CardNotFoundException;
use Makao\Table;

class CardActionService
{
    /**
     * @var Table
     */
    private Table $table;
    private int $cardToGet = 0;
    private int $actionCount = 0;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function afterCard(Card $card): void
    {
        $this->table->finishRound();
        switch ($card->getValue()) {
            case Card::VALUE_TWO:
                $this->cardTwoAction();
                break;
            case Card::VALUE_THREE:
                $this->cardThreeAction();
                break;
            case Card::VALUE_FOUR:
                $this->skipRound();
                break;
            default:
                break;
        }
    }

    private function cardTwoAction(): void
    {
        $this->cardToGet += 2;
        $player = $this->table->getCurrentPlayer();
        try {
            $card = $player->pickCardByValue(Card::VALUE_TWO);
            $this->table->getPlayedCards()->add($card);
            $this->table->finishRound();
            $this->cardTwoAction();
        } catch (CardNotFoundException $e) {
            $this->playerTakeCards($this->cardToGet);
        }
    }

    private function cardThreeAction(): void
    {
        $this->cardToGet += 3;
        $player = $this->table->getCurrentPlayer();
        try {
            $card = $player->pickCardByValue(Card::VALUE_THREE);
            $this->table->getPlayedCards()->add($card);
            $this->table->finishRound();
            $this->cardThreeAction();
        } catch (CardNotFoundException $e) {
            $this->playerTakeCards($this->cardToGet);
        }
    }

    /**
     */
    private function playerTakeCards(int $count): void
    {
        $player = $this->table->getCurrentPlayer();

        $player
            ->takeCards($this->table->getCardDeck(), $count);
        $this->table->finishRound();
    }

    public function skipRound() : void
    {
        ++$this->actionCount;
        $player = $this->table->getCurrentPlayer();
        try {
            $card = $player->pickCardByValue(Card::VALUE_FOUR);
            $this->table->getPlayedCards()->add($card);
            $this->table->finishRound();
            $this->skipRound();
        } catch (CardNotFoundException $e) {
             $player->addRoundToSkip($this->actionCount - 1);
             $this->table->finishRound();
        }
    }
}