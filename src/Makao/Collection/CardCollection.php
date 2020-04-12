<?php


namespace Makao\Collection;


use ArrayAccess;
use Countable;
use Iterator;
use Makao\Card;
use Makao\Exception\MethodNotAllowedException;

class CardCollection implements Countable, Iterator, ArrayAccess
{
	private const FIRST_CARD_INDEX = 0;
	private int $position = self::FIRST_CARD_INDEX;
	private array $cards = [];

	public function __construct (array $cards = [])
	{
		$this->cards = $cards;
	}

	/**
	 * @inheritDoc
	 */
	public function count (): int
	{
		return count($this->cards);
	}

	public function add (Card $card)
	{
		$this->cards[] = $card;
		return $this;
	}

	public function addCollection (CardCollection $cardCollection) : self
	{
		foreach (clone $cardCollection as $card)
		{
			$this->add($card);
		}
		return $this;
	}

	public function pickCard (): Card
	{
		if (empty($this->cards))
			throw new CardNotFoundException('You can not pick card from empty card collection!');

		$pickedCard = $this->offsetGet(self::FIRST_CARD_INDEX);
		$this->offsetUnset(self::FIRST_CARD_INDEX);
		$this->cards = array_values($this->cards);
		return $pickedCard;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet ($offset)
	{
		return $this->cards[$offset];
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset ($offset)
	{
		unset($this->cards[$offset]);
	}

	/**
	 * @inheritDoc
	 */
	public function current (): Card
	{
		return $this->cards[$this->position];
	}

	/**
	 * @inheritDoc
	 */
	public function next (): void
	{
		++$this->position;
	}

	/**
	 * @inheritDoc
	 */
	public function key (): int
	{
		return $this->position;
	}

	/**
	 * @inheritDoc
	 */
	public function valid ()
	{
		if (isset($this->cards[$this->position]))
		{
			return true;
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function rewind ()
	{
		$this->position = self::FIRST_CARD_INDEX;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists ($offset)
	{
		return isset($this->cards[$offset]);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet ($offset, $value)
	{
		throw new MethodNotAllowedException('You cannot add card to collection as array. Use add card method');
	}

	public function toArray()
	{
		return $this->cards;
	}
}