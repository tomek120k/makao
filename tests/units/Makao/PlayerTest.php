<?php


namespace Tests\Makao;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Collection\CardNotFoundException;
use Makao\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    public function testShouldWritePlayerName(): void
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

    public function testShouldReturnPlayerCardCollection(): void
    {
        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE)
            ]
        );
        $player = new Player('Tom', $cardCollection);
        // When
        $actual = $player->getCards();
        // Then
        $this->assertSame($cardCollection, $actual);
    }

    public function testShouldAllowPlayerTakeCardFromDeck(): void
    {
        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_ACE);
        $cardCollection = new CardCollection([$card]);
        $player = new Player('Tom');
        // When
        $actual = $player->takeCards($cardCollection)->getCards();
        // Then
        $this->assertCount(0, $cardCollection);
        $this->assertCount(1, $player->getCards());
        $this->assertSame($card, $actual[0]);
    }

    public function testShouldAllowPlayerTakeManyCardsFromCardCollection(): void
    {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_ACE);
        $secondCard = new Card(Card::COLOR_HEART, Card::VALUE_EIGHT);
        $thirdCard = new Card(Card::COLOR_SPADE, Card::VALUE_KING);
        $cardCollection = new CardCollection(
            [
                $firstCard,
                $secondCard,
                $thirdCard
            ]
        );
        $player = new Player('Tom');
        // When
        $actual = $player->takeCards($cardCollection, 2)->getCards();
        // Then
        $this->assertCount(1, $cardCollection);
        $this->assertCount(2, $player->getCards());
        $this->assertSame($firstCard, $actual->pickCard());
        $this->assertSame($secondCard, $actual->pickCard());
        $this->assertSame($thirdCard, $cardCollection->pickCard());
    }

    public function testShouldAllowPickChosenCardFromPlayerCardCollection()
    {
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_ACE);
        $secondCard = new Card(Card::COLOR_HEART, Card::VALUE_EIGHT);
        $thirdCard = new Card(Card::COLOR_SPADE, Card::VALUE_KING);
        $cardCollection = new CardCollection(
            [
                $firstCard,
                $secondCard,
                $thirdCard
            ]
        );

        // Given
        $player = new Player('Tom', $cardCollection);
        // When
        $actual = $player->pickCard(2);
        // Then
        $this->assertSame($thirdCard, $actual);
    }

    public function testShouldAllowPlayerSaysMakao()
    {
        // Given
        $player = new Player('Tom');
        // When
        $actual = $player->sayMakao();
        // Then
        $this->assertEquals('Makao', $actual);
    }

    public function testShouldThrowCardNotFoundExceptionWhenPlayTryPickCardByValueAndHasNotCorrectCardInHand()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('Player has not card with value 2');
    	// Given
        $player = new Player('Andy');
    	// When
        $player->pickCardByValue(Card::VALUE_TWO);
    	// Then
    }

    public function testShouldReturnPickCardByValueWhenPlayerHasCorrectCard()
    {
        // Given
        $card = new Card(Card::COLOR_HEART, Card::VALUE_TWO);
        $player = new Player('Andy', new CardCollection([$card]));
        // When
        $actual = $player->pickCardByValue(Card::VALUE_TWO);
        // Then
        $this->assertSame($actual, $card);
    }

    public function testShouldReturnFirstCardByValueWhenPlayerHasMoreCorrectCard()
    {
         // Given
        $card = new Card(Card::COLOR_HEART, Card::VALUE_TWO);
        $player = new Player('Andy', new CardCollection([
            $card,
            new Card(Card::COLOR_SPADE, Card::VALUE_TWO)
        ]));
        // When
        $actual = $player->pickCardByValue(Card::VALUE_TWO);
        // Then
        $this->assertSame($actual, $card);
    }
}
