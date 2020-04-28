<?php


namespace Tests\Makao\Collection;


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Collection\CardNotFoundException;
use Makao\Exception\MethodNotAllowedException;
use PHPUnit\Framework\TestCase;

class CardCollectionTest extends TestCase
{
    /**
     * @var CardCollection
     */
    private CardCollection $cardCollectionUnderTest;

    public function setUp(): void
    {
        $this->cardCollectionUnderTest = new CardCollection();
    }

    public function testShouldReturnZeroOnEmptyCollection()
    {
        // Then
        $this->assertCount(0, $this->cardCollectionUnderTest);
    }

    public function testShouldAddNewCardToCollection()
    {
        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $expected = 1;
        // When
        $this->cardCollectionUnderTest->add($card);
        // Then
        $this->assertCount($expected, $this->cardCollectionUnderTest);
    }

    public function testShouldAddNewCardsInChainToCardCollection()
    {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $secondCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $expected = 2;
        // When
        $this->cardCollectionUnderTest
            ->add($firstCard)
            ->add($secondCard);
        // Then
        $this->assertCount($expected, $this->cardCollectionUnderTest);
    }

    public function testShouldThrowCardNotFoundExceptionWhenTryPickFromEmptyCardCollection()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('You can not pick card from empty card collection!');
        // When
        $this->cardCollectionUnderTest->pickCard();
        // Then
    }

    public function testShouldItarableCardCollection()
    {
        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        // When
        $this->cardCollectionUnderTest->add($card);
        // Then
        $this->assertTrue($this->cardCollectionUnderTest->valid());
        $this->assertSame($card, $this->cardCollectionUnderTest->current());
        $this->assertSame(0, $this->cardCollectionUnderTest->key());

        $this->cardCollectionUnderTest->next();
        $this->assertFalse($this->cardCollectionUnderTest->valid());
        $this->assertSame(1, $this->cardCollectionUnderTest->key());

        $this->cardCollectionUnderTest->rewind();
        $this->assertTrue($this->cardCollectionUnderTest->valid());
        $this->assertSame($card, $this->cardCollectionUnderTest->current());
        $this->assertSame(0, $this->cardCollectionUnderTest->key());
    }

    public function testShouldGetFirstCardFromCollectionAndRemoveThisCardFromDeck()
    {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $secondCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $expected = 1;
        $this->cardCollectionUnderTest
            ->add($firstCard)
            ->add($secondCard);
        // When
        $acctual = $this->cardCollectionUnderTest->pickCard();
        // Then
        $this->assertCount($expected, $this->cardCollectionUnderTest);

        $this->assertSame($firstCard, $acctual);
        $this->assertSame($secondCard, $this->cardCollectionUnderTest[0]);
    }

    public function testShouldThrowCardNotFoundExceptionWhenIPickedAllFromCardCollection()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('You can not pick card from empty card collection!');
        // When
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $secondCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $this->cardCollectionUnderTest
            ->add($firstCard)
            ->add($secondCard);
        // Then
        $actual = $this->cardCollectionUnderTest->pickCard();
        $this->assertSame($firstCard, $actual);

        $actual = $this->cardCollectionUnderTest->pickCard();
        $this->assertSame($secondCard, $actual);

        $this->cardCollectionUnderTest->pickCard();
    }

    public function testShouldReturnChosenCardPickedFromCollection()
    {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $secondCard = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        $this->cardCollectionUnderTest->add($firstCard)->add($secondCard);
        // When
        $actual = $this->cardCollectionUnderTest->pickCard(1);
        // Then
        $this->assertSame($secondCard, $actual);
    }

    public function testShouldThrowMethodNotAllowedExceptionWhenYouTryAddCardToCollectoinAsArray()
    {
        // Expect
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('You cannot add card to collection as array. Use add card method');
        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_KING);
        // When

        // Then
        $this->cardCollectionUnderTest[] = $card;
    }

    public function testShouldReturnCollectionAsArray()
    {
        // Given
        $cards = [
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING)
        ];

        // When
        $actual = new CardCollection($cards);
        // Then
        $this->assertEquals($cards, $actual->toArray());
    }

    public function testShouldAddCardCollectoinToCardCollection()
    {
        // Expect

        // Given
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR)
            ]
        );

        // When
        $actual = $this->cardCollectionUnderTest->addCollection($cardCollection);
        // Then
        $this->assertEquals($cardCollection, $actual);
    }

    public function testShouldReturnLastCardFromCollectionWithoutPicking(): void
    {
        // Given
        $lastCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_FOUR);
        $cardCollection = new CardCollection(
            [
                new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                $lastCard
            ]
        );

        // When
        $actual = $cardCollection->getLastCard();
        // Then
        $this->assertSame($lastCard, $actual);
    }

    public function testShouldThrowCardNotFoundExceptionWhenTryGetLastFromEmptyCollection()
    {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('You can not get last card from empty card collection!');

        // Then
        $actual = $this->cardCollectionUnderTest->getLastCard();
    }
}