<?php


namespace Tests\Makao;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
	public function testShouldWritePlayerName()
	{
	    // Given
		$player = new Player('Andrew');
	    // When
		ob_start();
		echo $player;
		$actual = ob_get_clean();
	    // Then
		$this->assertEquals('Andrew', $actual);
	}
	
	public function testShouldReturnPlayerCardCollection()
	{
	    // Given
		$cardCollection = new CardCollection([
			new Card(Card::COLOR_CLUB, Card::VALUE_ACE)
		]);
		$player = new Player('Tom', $cardCollection);
	    // When
		$actual = $player->getCards();
	    // Then
		$this->assertSame($cardCollection, $actual);
	}
	
	public function testShouldAllowPlayerTakeCardFromDeck()
	{
	    // Given
		$card = new Card(Card::COLOR_CLUB, Card::VALUE_ACE);
		$cardCollection = new CardCollection([$card]);
		$player = new Player('Tom');
	    // When
		$actual = $player->takeCard($cardCollection)->getCards();
	    // Then
		$this->assertCount(0, $cardCollection);
		$this->assertCount(1, $player->getCards());
		$this->assertSame($card, $actual[0]);
	}
}
