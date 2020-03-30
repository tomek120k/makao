<?php

namespace Tests\Makao\Service;


use Makao\Player;
use Makao\Service\GameService;
use PHPUnit\Framework\TestCase;


class GameServiceTest extends TestCase
{
	/**
	 * @var GameService
	 */
	private GameService $gameServiceUnderTest;

	public function testShouldReturnFalseWhenGameIsNotStarted() : void
	{
		// When
		$actual = $this->gameServiceUnderTest->isStarted();
		// Then
		$this->assertFalse($actual);
	}

	public function testShouldInitNewGameWithEmptyTable() : void
	{
		// When
		$table = $this->gameServiceUnderTest->getTable();
		// Then
		$this->assertSame(0, $table->countPlayers());
		$this->assertCount(0, $table->getCardDeck());
		$this->assertCount(0, $table->getPlayedCards());
	}

	public function testShouldAddPlayersToTheTable() : void
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

	public function testShouldReturnTrueWhenGameIsStarted() : void
	{
		// When
		$this->gameServiceUnderTest->startGame();
		// Then
		$this->assertTrue($this->gameServiceUnderTest->isStarted());
	}

	protected function setUp (): void
	{
		$this->gameServiceUnderTest = new GameService();
	}

}