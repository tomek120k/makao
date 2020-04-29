<?php

namespace Tests\Makao\Validator;

use Makao\Card;
use Makao\Exception\CardDuplicationException;
use Makao\Validator\CardValidator;
use PHPUnit\Framework\TestCase;

class CardValidatorTest extends TestCase
{

    /**
     * @var CardValidator
     */
    private CardValidator $cardValidator;

    public function cardsProvider()
    {
        return [
            'testShouldReturnTrueWhenValidCardsWithTheSameColors' => [
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
                true
            ],
            'testShouldReturnTrueWhenValidCardsWithTheSameValues' => [
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
                true
            ],
            'testShouldReturnFaleWhenValidCardsWithTheDiffrentColors' => [
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
                false
            ],
            'testShouldReturnFaleWhenValidCardsWithTheDiffrentValues' => [
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
                false
            ],
            'Queens for all' => [
                new Card(Card::COLOR_HEART, Card::VALUE_TEN),
                new Card(Card::COLOR_DIAMOND, Card::VALUE_QUEEN),
                true
            ],
            'All for Queens' => [
                new Card(Card::COLOR_DIAMOND, Card::VALUE_QUEEN),
                new Card(Card::COLOR_HEART, Card::VALUE_TEN),
                true
            ]

        ];
    }

    /**
     * @dataProvider cardsProvider
     * @param Card $activeCard
     * @param Card $newCard
     * @param bool $expected
     */
    public function testShouldValidCards(Card $activeCard, Card $newCard, bool $expected): void
    {
        // When
        $actual = $this->cardValidator->valid($activeCard, $newCard);
        // Then
        $this->assertSame($expected, $actual);
    }

    public function testShouldThrowCardDuplicationExceptionWhenValidCardsAreTheSmae()
    {
        // Expect
        $this->expectException(CardDuplicationException::class);
        $this->expectExceptionMessage('Valid cards are the same cards: 5 spade');
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FIVE);
        // When
        $this->cardValidator->valid($card, $card);
    }

    protected function setUp(): void
    {
        $this->cardValidator = new CardValidator();
    }

}
