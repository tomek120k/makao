<?php

namespace Tests\Makao\Service;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Collection\CardNotFoundException;
use Makao\Exception\GameException;
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
    /**
     * @var CardService|MockObject
     */
    private $cardServiceMock;

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
        $this->gameServiceUnderTest->getTable()->addCardCollectionToDeck(new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
            new Card(Card::COLOR_HEART, Card::VALUE_TWO),
            new Card(Card::COLOR_HEART, Card::VALUE_THREE),
            new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
            new Card(Card::COLOR_HEART, Card::VALUE_JACK),
            new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
            new Card(Card::COLOR_HEART, Card::VALUE_KING),
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),

            new Card(Card::COLOR_SPADE, Card::VALUE_TWO),
            new Card(Card::COLOR_SPADE, Card::VALUE_THREE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
            new Card(Card::COLOR_SPADE, Card::VALUE_JACK),
            new Card(Card::COLOR_SPADE, Card::VALUE_QUEEN),
            new Card(Card::COLOR_SPADE, Card::VALUE_KING),
            new Card(Card::COLOR_SPADE, Card::VALUE_ACE),

            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
        ]));
        $this->gameServiceUnderTest->addPlayers([
            new Player('Andy'),
            new Player('Max')
        ]);
        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
         $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($this->gameServiceUnderTest->getTable()->getCardDeck())
            ->willReturn($noActionCard);
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

    public function testShouldThrowGameExceptionWhenStartGameWithoutCardDeck(): void
    {
        // Expect
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Prepare card deck before game start');
        // Given
        $this->gameServiceUnderTest->startGame();
    }

    public function testShouldThrowGameExceptionWhenStartGameWithoutMinimalPlayers(): void
    {
        // Expect
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('You need minimum 2 players to start game');
        // Given
        $this->gameServiceUnderTest->getTable()->addCardCollectionToDeck(new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE)
        ]));
        $this->gameServiceUnderTest->startGame();
    }

    public function testShouldChooseNonActionCardAsFirstPlayedCardWhenStartGame(): void
    {
        // Given
        $table = $this->gameServiceUnderTest->getTable();
        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection(
            [
                new Card(Card::COLOR_HEART, Card::VALUE_TWO),
                $noActionCard,
                new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),

                new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
            new Card(Card::COLOR_HEART, Card::VALUE_TWO),
            new Card(Card::COLOR_HEART, Card::VALUE_THREE),
            new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
            new Card(Card::COLOR_HEART, Card::VALUE_JACK),
            new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
            new Card(Card::COLOR_HEART, Card::VALUE_KING),
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),

            new Card(Card::COLOR_SPADE, Card::VALUE_TWO),
            new Card(Card::COLOR_SPADE, Card::VALUE_THREE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
            new Card(Card::COLOR_SPADE, Card::VALUE_JACK),
            new Card(Card::COLOR_SPADE, Card::VALUE_QUEEN),
            new Card(Card::COLOR_SPADE, Card::VALUE_KING),
            new Card(Card::COLOR_SPADE, Card::VALUE_ACE),

            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),

            ]
        );
        $this->gameServiceUnderTest->addPlayers([
            new Player('Andy'),
            new Player('Max')
        ]);
        $table->addCardCollectionToDeck($collection);
        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willReturn($noActionCard);
        // When
        $this->gameServiceUnderTest->startGame();
        // Then
        $this->assertCount(1, $table->getPlayedCards());
        $this->assertSame($noActionCard, $table->getPlayedCards()->pickCard());
    }

    public function testShouldThrowGameExceptionWhenCardServiceThrowException(): void
    {
        // Expect
        $cardNotFoundException = new CardNotFoundException('No regular cards in collection');
        $gameException = new GameException('The game needs help!', $cardNotFoundException);
        $this->expectExceptionObject($gameException);
        $this->expectExceptionMessage('The game needs help! Issue: No regular cards in collection');
        // Given
        $this->gameServiceUnderTest->addPlayers([
            new Player('Andy'),
            new Player('Max')
        ]);
        $table = $this->gameServiceUnderTest->getTable();
        $noActionCard = new Card(Card::COLOR_HEART, Card::VALUE_FIVE);
        $collection = new CardCollection(
            [
                $noActionCard
            ]
        );
        $table->addCardCollectionToDeck($collection);
        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willThrowException($cardNotFoundException);
        // When
        $this->gameServiceUnderTest->startGame();
    }

    public function testShouldPlayersTakesFiveCardsFromDeckOnStartGame(): void
    {
        // Given
        $players = [
            new Player('Andy'),
            new Player('Tom'),
            new Player('Max')
        ];

        $this->gameServiceUnderTest->addPlayers($players);
        $table = $this->gameServiceUnderTest->getTable();
        $noActionCard = new Card(Card::COLOR_HEART, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_TWO),
            new Card(Card::COLOR_HEART, Card::VALUE_THREE),
            new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
            new Card(Card::COLOR_HEART, Card::VALUE_JACK),
            new Card(Card::COLOR_HEART, Card::VALUE_QUEEN),
            new Card(Card::COLOR_HEART, Card::VALUE_KING),
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),

            new Card(Card::COLOR_SPADE, Card::VALUE_TWO),
            new Card(Card::COLOR_SPADE, Card::VALUE_THREE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
            new Card(Card::COLOR_SPADE, Card::VALUE_JACK),
            new Card(Card::COLOR_SPADE, Card::VALUE_QUEEN),
            new Card(Card::COLOR_SPADE, Card::VALUE_KING),
            new Card(Card::COLOR_SPADE, Card::VALUE_ACE),

            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),

            $noActionCard
        ]);

        $table->addCardCollectionToDeck($collection);

        $this->cardServiceMock
            ->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willReturn($noActionCard);
        // When
        $this->gameServiceUnderTest->startGame();
        // Then
        foreach ($players as $player) {
            $this->assertCount(5, $player->getCards());
        }
    }


    protected function setUp(): void
    {
        $this->cardServiceMock = $this->createMock(CardService::class);
        $this->gameServiceUnderTest = new GameService(new Table(), $this->cardServiceMock);
    }

}