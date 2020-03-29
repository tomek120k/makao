<?php


namespace Makao;


use Makao\Collection\CardCollection;

class Player
{
	private $name = '';
	private $cardCollection = null;

	public function __construct (string $name = '', CardCollection $cardCollection = null)
	{
		$this->name = $name;
		$this->cardCollection = $cardCollection ?? new CardCollection();
	}

	public function getCards () : CardCollection
	{
		return $this->cardCollection;
	}

	public function takeCard (CardCollection $cardCollection) : self
	{
		$this->cardCollection->add($cardCollection->pickCard());
		return $this;
	}

	public function __toString () : string
	{
		return $this->name;
	}
}