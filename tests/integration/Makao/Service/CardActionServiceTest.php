<?php

namespace Tests\Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use Makao\Service\CardActionService;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class CardActionServiceTest extends TestCase
{
    /**
     * @var Player
     */
    private Player $player1;
    /**
     * @var Player
     */
    private Player $player2;
    /**
     * @var Player
     */
    private Player $player3;
    private CardCollection $playedCard;
    private Table $table;
    private CardActionService $serviceUnderTest;
    private CardCollection $deck;

    public function testShouldGiveNextPlayerTwoCardsWhenCardTwoWasDropped(): void
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(2, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveThirdPlayerFourCardsWhenCardTwoWasDroppedAndSecondPlayerHasCardTwoDefend()
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_TWO)
        );
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(4, $this->player3->getCards());
        $this->assertSame($this->player1, $this->table->getCurrentPlayer());
    }


    public function testShouldGiveFirstPlayerSixCardsWhenCardTwoWasDroppedAndSecondAndThirdPlayerHasCardTwoDefend()
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_TWO)
        );
         $this->player3->getCards()->add(
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO)
        );
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(6, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveSecendPlayerEightCardsWhenCardTwoWasDroppedAndSecondAndThirdPlayerHasCardTwoDefend()
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        $this->player1->getCards()->add(
            new Card(Card::COLOR_DIAMOND, Card::VALUE_TWO)
        );

        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_TWO)
        );
         $this->player3->getCards()->add(
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO)
        );
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(8, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(0, $this->player1->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    protected function setUp(): void
    {
        // Given
        $this->playedCard = new CardCollection(
            [
                new Card(Card::COLOR_SPADE, Card::VALUE_TWO),
                new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT)
            ]
        );

        $this->deck = new CardCollection(
            [
                new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
                new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
                new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
                new Card(Card::COLOR_SPADE, Card::VALUE_SIX),
                new Card(Card::COLOR_HEART, Card::VALUE_SIX),
                new Card(Card::COLOR_CLUB, Card::VALUE_SIX),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_SIX),
                new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN),
                new Card(Card::COLOR_HEART, Card::VALUE_SEVEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_SEVEN),
                new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT),
                new Card(Card::COLOR_HEART, Card::VALUE_EIGHT),
                new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_EIGHT),
                new Card(Card::COLOR_SPADE, Card::VALUE_NINE),
                new Card(Card::COLOR_HEART, Card::VALUE_NINE),
                new Card(Card::COLOR_CLUB, Card::VALUE_NINE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_NINE),
                new Card(Card::COLOR_SPADE, Card::VALUE_TEN),
                new Card(Card::COLOR_HEART, Card::VALUE_TEN),
                new Card(Card::COLOR_CLUB, Card::VALUE_TEN),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_TEN),
            ]
        );

        $this->player1 = new Player('Any');
        $this->player2 = new Player('Max');
        $this->player3 = new Player('Tom');

        $this->table = new Table($this->deck, $this->playedCard);
        $this->table->addPlayer($this->player1);
        $this->table->addPlayer($this->player2);
        $this->table->addPlayer($this->player3);

        $this->serviceUnderTest = new CardActionService($this->table);
    }
}