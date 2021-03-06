<?php

namespace Tests\Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use Makao\Service\CardActionService;
use Makao\Table;
use PHPUnit\Framework\TestCase;

use function var_dump;

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

    public function testShouldGiveNextPlayerThreeCardsWhenCardThreeWasDropped(): void
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(3, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveThirdPlayerSixCardsWhenCardThreeWasDroppedAndSecondPlayerHasCardThreeDefend() : void
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);
        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_THREE)
        );
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(6, $this->player3->getCards());
        $this->assertSame($this->player1, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveFirstPlayerNineCardsWhenCardThreeWasDroppedAndSecondAndThirdPlayerHasCardTwoDefend() : void
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);
        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_THREE)
        );
         $this->player3->getCards()->add(
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE)
        );
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(9, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveSecendPlayerTwelveCardsWhenCardThreeWasDroppedAndSecondAndThirdPlayerHasCardTwoDefend() : void
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);
        $this->player1->getCards()->add(
            new Card(Card::COLOR_DIAMOND, Card::VALUE_THREE)
        );

        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_THREE)
        );
         $this->player3->getCards()->add(
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE)
        );
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(12, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(0, $this->player1->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    //-------

    public function testShouldGiveNextPlayerTwoCardsWhenCardTwoWasDropped(): void
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(2, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveThirdPlayerFourCardsWhenCardTwoWasDroppedAndSecondPlayerHasCardTwoDefend() : void
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


    public function testShouldGiveFirstPlayerSixCardsWhenCardTwoWasDroppedAndSecondAndThirdPlayerHasCardTwoDefend() : void
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

    public function testShouldGiveSecendPlayerEightCardsWhenCardTwoWasDroppedAndSecondAndThirdPlayerHasCardTwoDefend() : void
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

    public function testShouldSkipRoundForNextPlayerWhenCardFourWasDropped() : void
    {
        // Given
         $card = new Card(Card::COLOR_SPADE, Card::VALUE_FOUR);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldSkipManyRoundsForNextPlayerWhenCardFourWasDroppedAndNextPlayersHaveCardsFourToDefend(): void
    {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FOUR);
        $this->player2->getCards()->add(
            new Card(Card::COLOR_HEART, Card::VALUE_FOUR)
        );
         $this->player3->getCards()->add(
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR)
        );
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
        $this->assertEquals(2, $this->player1->getRoundToSkip());
        $this->assertFalse( $this->player1->canPlayRound());
        $this->assertTrue( $this->player2->canPlayRound());
        $this->assertTrue( $this->player3->canPlayRound());
    }

    public function testShouldRequestCardByValueWhenCardJackWasDroppedAndTakeCardForEachPlayerWhenTheyNotHaveRequstedCard(): void
    {
        // Given
        $requestedValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_JACK);
        $requestedCard = new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN);
        $this->player1->getCards()->add($requestedCard);
        // When
        $this->serviceUnderTest->afterCard($card, $requestedValue);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(1, $this->player2->getCards());
        $this->assertCount(1, $this->player3->getCards());
        $this->assertSame($requestedCard, $this->table->getPlayedCards()->getLastCard());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldRequestCardByValueWhenCardJackWasDroppedAndPickCardsForEachPlayerWhenTheyHaveRequstedCard(): void
    {
        // Given
        $requestedValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_JACK);

        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN));
        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_SEVEN));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN));
        // When
        $this->serviceUnderTest->afterCard($card, $requestedValue);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldAllowDropManyRequestedCardsByValueWhenPlayerDropJackCard(): void
    {
        // Given
        $requestedValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_JACK);
        $requestedCard = new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN);
        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN));
        $this->player1->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_SEVEN));
        $this->player1->getCards()->add($requestedCard);
        // When
        $this->serviceUnderTest->afterCard($card, $requestedValue);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(1, $this->player2->getCards());
        $this->assertCount(1, $this->player3->getCards());
        $this->assertSame($requestedCard, $this->table->getPlayedCards()->getLastCard());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveNextPlayerFiveCardsWhenCardKingHeartWasDropped(): void
    {
        $card = new Card(Card::COLOR_HEART, Card::VALUE_KING);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(5, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGivePreviousPlayerFiveCardsWhenCardKingPicWasDropped(): void
    {
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_KING);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(5, $this->player3->getCards());
        $this->assertSame($this->player1, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveCurrentPlayerTenCardsWhenCardKingHeartWasDroppedAndNextPlayerHasKingSpadeToDefence(): void
    {
        $card = new Card(Card::COLOR_HEART, Card::VALUE_KING);
        $this->player2->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_KING));
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(10, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveCurrentPlayerTenCardsWhenCardKingSpadeWasDroppedAndPreviousPlayerHasKingHearToDefence(): void
    {

        $card = new Card(Card::COLOR_SPADE, Card::VALUE_KING);

        $this->player3->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_KING));
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(10, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldNotRunAnyActionForOtherKings(): void
    {
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());

        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldNotRunAnyActionForAnyNonActionCard(): void
    {
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        // When
        $this->serviceUnderTest->afterCard($card);
        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());

        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldChangeColorToPlayOnTableAfterCardAce(): void
    {
        // Given
        $requestColor = Card::COLOR_HEART;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_ACE);
        // Then & When
        $this->assertEquals(Card::COLOR_SPADE, $this->table->getPlayedCardColor());
        $this->serviceUnderTest->afterCard($card, $requestColor);
        $this->assertEquals($requestColor, $this->table->getPlayedCardColor());
    }
}