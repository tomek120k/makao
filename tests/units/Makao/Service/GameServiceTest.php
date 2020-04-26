<?php

namespace Tests\Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use Makao\Service\CardService;
use Makao\Service\GameService;
use Makao\Table;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class GameServiceTest extends TestCase
{
    /**
     * @var GameService
     */
    private GameService $gameServiceUnderTest;
    private $mockObject;

    public function testShouldReturnFalseWhenGameIsNotStarted(): void
    {
        // When
        $actual = $this->gameServiceUnderTest->isStarted();
        // Then
        $this->assertFalse($actual);
    }

    public function testShouldInitNewGameWithEmptyTable(): void
    {
        // When
        $table = $this->gameServiceUnderTest->getTable();
        // Then
        $this->assertSame(0, $table->countPlayers());
        $this->assertCount(0, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
    }

    public function testShouldAddPlayersToTheTable(): void
    {
        //GIVEN
        $players = [
            new Player('Tom'),
            new Player('Andy')
        ];
        //WHEN
        $actual = $this->gameServiceUnderTest
            ->addPlayers($players)
            ->getTable();

        //THEN
        $this->assertSame(2, $actual->countPlayers());
    }

    public function testShouldReturnTrueWhenGameIsStarted(): void
    {
        // When
        $this->gameServiceUnderTest->startGame();
        // Then
        $this->assertTrue($this->gameServiceUnderTest->isStarted());
    }

    /**
     * @throws \ReflectionException
     */
    public function testShouldCreateShuffledCardDesk()
    {
        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR)
            ]
        );

        $shuffledCardCollection = new CardCollection(
            [
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR),
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE)
            ]
        );
        /**
         * @var MockObject | Table $tableStub
         */
        $tableMock = $this->createMock(Table::class);

        /**
         * @var MockObject | CardService $cardServiceMock
         */

        $this->cardServiceMock->expects($this->once())
            ->method('createDeck')
            ->willReturn($cardCollection);
        $this->cardServiceMock->expects(($this->once()))
            ->method('shuffle')
            ->with($cardCollection)
            ->willReturn($shuffledCardCollection);


        // When
        /**
         * @var Table $table
         */
        $table = $this->gameServiceUnderTest->prepareCardDeck();
        // Then
        $this->assertCount(2, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
        $this->assertEquals($shuffledCardCollection, $table->getCardDeck());
    }

    protected function setUp(): void
    {
        $this->cardServiceMock = $this->createMock(CardService::class);
        $this->gameServiceUnderTest = new GameService(new Table(), $this->cardServiceMock);
    }

}