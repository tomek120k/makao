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

    public function afterCard(Card $card, string $request = null): void
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
            case Card::VALUE_JACK:
                $this->requestingCardValue($request);
                break;
            case Card::VALUE_KING:
                $this->afterKing($card->getColor());
                break;
            case Card::VALUE_ACE;
                $this->changePleyedColorCardOnTable($request);
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

    /**
     */
    private function playerTakeCards(int $count): void
    {
        $player = $this->table->getCurrentPlayer();

        $player
            ->takeCards($this->table->getCardDeck(), $count);
        $this->table->finishRound();
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

    public function skipRound(): void
    {
        ++$this->actionCount;
        $player = $this->table->getCurrentPlayer();
        try {
            $card = $player->pickCardByValue(Card::VALUE_FOUR);
            $this->table->addPlayedCard($card);
            $this->table->finishRound();
            $this->skipRound();
        } catch (CardNotFoundException $e) {
            $player->addRoundToSkip($this->actionCount - 1);
            $this->table->finishRound();
        }
    }

    private function requestingCardValue(?string $cardValue): void
    {
        $iteration = $this->table->countPlayers();
        for ($i = 0; $i < $iteration; $i++) {
            $player = $this->table->getCurrentPlayer();
            try {
                $cards = $player->pickCardsByValue($cardValue);
                $this->table->addPlayedCards($cards);
            } catch (CardNotFoundException $e) {
                $player->takeCards($this->table->getCardDeck());
            } finally {
                $this->table->finishRound();
            }
        }
    }

    private function afterKing(string $color): void
    {
        $this->actionCount += 5;
        switch ($color) {
            case Card::COLOR_HEART:
                $this->afterKingHeart();
                break;
            case Card::COLOR_SPADE:
                $this->afterKingSpade();
                break;
            default:
                break;
        }
    }

    private function afterKingHeart(): void
    {
        $player = $this->table->getCurrentPlayer();
        try {
            $card = $player->pickCardByValueAndColor(Card::VALUE_KING, Card::COLOR_SPADE);
            $this->table->addPlayedCard($card);
            $this->table->finishRound();
            $this->afterKing(Card::COLOR_SPADE);
        } catch (CardNotFoundException $e) {
            $this->playerTakeCards($this->actionCount);
        }
    }

    private function afterKingSpade(): void
    {
        $this->table->backRound();
        $player = $this->table->getPreviousPlayer();
        try {
            $card = $player->pickCardByValueAndColor(Card::VALUE_KING, Card::COLOR_HEART);
            $this->table->addPlayedCard($card);
            $this->afterKing(Card::COLOR_HEART);
        } catch (CardNotFoundException $e) {
            $this->table->backRound();
            $this->playerTakeCards($this->actionCount);
        }
    }

    private function changePleyedColorCardOnTable(?string $color)
    {
        $this->table->changePlayedCardColor($color);
    }
}