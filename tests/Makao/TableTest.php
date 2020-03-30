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

	public function setUp (): void
	{
		$this->tableUnderTest = new Table();
	}

	public function testShouldCreateEmptyTable (): void
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
		$cards = new CardCollection([
			new Card(Card::COLOR_SPADE, CARD::VALUE_EIGHT)
		]);
	    // When
		$table = new Table($cards);
		$actual = $table->getCardDeck();
	    // Then
		$this->assertSame($cards, $actual);
	}
}
