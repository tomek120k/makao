<?php


use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Collection\CardNotFoundException;
use Makao\Service\CardService;
use Makao\Service\ShuffleService;
use PHPUnit\Framework\TestCase;

class CardServiceTest extends TestCase
{
    /**
     * @var CardService|null
     */
    private $cardServiceUnderTest = null;
    /**
     * @var ShuffleService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $shuffleServiceMock;

    public function testShouldAllowCreateNewCardCollection()
    {
        // Given
        $actual = $this->cardServiceUnderTest->createDeck();
        // When

        // Then
        $this->assertInstanceOf(CardCollection::class, $actual);
        $this->assertCount(52, $actual);

        $i = 0;
        foreach (Card::values() as $value) {
            foreach (Card::colors() as $color) {
                $this->assertEquals($value, $actual[$i]->getValue());
                $this->assertEquals($color, $actual[$i]->getColor());
                $i++;
            }
        }

        return $actual;
    }

    /**
     * @depends testShouldAllowCreateNewCardCollection
     */
    public function testShouldShuffleCardsInCardCollection(CardCollection $cardCollection)
    {
        // Given


        $this->shuffleServiceMock
            ->expects($this->once())
            ->method('shuffle')
            ->willReturn(array_reverse($cardCollection->toArray()));

        // When
        $actual = $this->cardServiceUnderTest->shuffle($cardCollection);
        // Then
        $this->assertNotEquals($cardCollection, $actual);
        $this->assertEquals($cardCollection->pickCard(), $actual[51]);
    }

    public function testShouldPickFirstNonActionCardFromCollection(): void
    {
        // Given
        $noActonCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            $noActonCard
                           ]);
        // When
        $actual = $this->cardServiceUnderTest->pickFirstNoActionCard($collection);
        // Then
        $this->assertCount(7, $collection);
        $this->assertSame($noActonCard, $actual);
    }

     public function testShouldPickFirstNonActionCardFromCollectionAndMovePreviousActionCardsOnTheEnd(): void
    {
        // Given
        $noActonCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            $noActonCard,
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),

                           ]);

          $expectCollection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),

                           ]);
        // When
        $actual = $this->cardServiceUnderTest->pickFirstNoActionCard($collection);
        // Then
        $this->assertCount(7, $collection);
        $this->assertEquals($expectCollection, $collection);
    }

    public function testShouldThrowCardNotFoundExceptionWhenPickFirstNoActionCardFromCollectionWithOnlyActionCards(): void
    {
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('No regular cards in collection');
        // Given
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
                           ]);

        //When
        $actual = $this->cardServiceUnderTest->pickFirstNoActionCard($collection);
    }

    protected function setUp(): void
    {
        $this->shuffleServiceMock = $this->createMock(ShuffleService::class);
        $this->cardServiceUnderTest = new CardService($this->shuffleServiceMock);
    }
}