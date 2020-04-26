<?php

namespace Tests\Makao;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    /**
     * @var Table
     */
    private Table $tableUnderTest;

    public function setUp(): void
    {
        $this->tableUnderTest = new Table();
    }

    public function testShouldCreateEmptyTable(): void
    {
        // Given
        $expected = 0;
        // When
        $acctual = $this->tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $acctual);
    }

    public function testShouldAddOnePlayerToTable(): void
    {
        // Given

        $player = new Player();
        $expected = 1;
        // When
        $this->tableUnderTest->addPlayer($player);
        $acctual = $this->tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $acctual);
    }

    public function testShouldReturnCountWhenIAddManyPlayers(): void
    {
        // Given
        $player = new Player();
        $expected = 2;
        // When
        $this->tableUnderTest->addPlayer($player);
        $this->tableUnderTest->addPlayer(new Player());
        $acctual = $this->tableUnderTest->countPlayers();
        // Then
        $this->assertSame($expected, $acctual);
    }

    public function testShouldThrowTooManyPlayersAtTheTableExceptionWhenTryAddMoreThanFourPlayers(): void
    {
        // Expect
        $this->expectException(TooManyPlayersAtTheTableException::class);
        $this->expectExceptionMessage('Max capacity is 4 players!');
        // Given
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
    }

    public function testShouldReturnEmptyCardCollectionForPlayedCard()
    {
        // When
        $actual = $this->tableUnderTest->getPlayedCards();
        // Then
        $this->assertInstanceOf(CardCollection::class, $actual);
        $this->assertCount(0, $actual);
    }

    public function testShouldPutCardDeckOnTable()
    {
        // Given
        $cards = new CardCollection(
            [
                new Card(Card::COLOR_SPADE, CARD::VALUE_EIGHT)
            ]
        );
        // When
        $table = new Table($cards);
        $actual = $table->getCardDeck();
        // Then
        $this->assertSame($cards, $actual);
    }

    public function testShouldAddCardCollectionToCardDeckOnTable()
    {
        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR)
            ]
        );
        // When
        $actual = $this->tableUnderTest->addCardCollectionToDeck($cardCollection);
        // Then
        $this->assertEquals($cardCollection, $actual->getCardDeck());
    }


    public function testShouldReturnCurrentPlayer()
    {
        // Given
        $player1 = new Player('Tom');
        $player2 = new Player('Andrew');
        $player3 = new Player('Peter');
        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
        // When
        $actual = $this->tableUnderTest->getCurrentPlayer();
        // Then
        $this->assertSame($player1, $actual);
    }

    public function testShouldReturnNextPlayer()
    {
        // Given
        $player1 = new Player('Tom');
        $player2 = new Player('Andrew');
        $player3 = new Player('Peter');
        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
        // When
        $actual = $this->tableUnderTest->getNextPlayer();
        // Then
        $this->assertSame($player2, $actual);
    }

    public function testShouldReturnPreviousPlayer()
    {
        // Given
        $player1 = new Player('Tom');
        $player2 = new Player('Andrew');
        $player3 = new Player('Peter');
        $player4 = new Player('Bill');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
        $this->tableUnderTest->addPlayer($player4);
        // When
        $actual = $this->tableUnderTest->getPreviousPlayer();
        // Then
        $this->assertSame($player4, $actual);
    }

    public function testShouldSwitchCurrentPlayerWhenRoundFinished()
    {
        // Given
        $player1 = new Player('Tom');
        $player2 = new Player('Andrew');
        $player3 = new Player('Peter');
        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);
        // When & Then
        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player2, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player1, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player3, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player1, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());
    }


}
