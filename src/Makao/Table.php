<?php


namespace Makao;


use Makao\Exception\TooManyPlayersAtTheTableException;

class Table
{
	const MAX_PLAYERS = 4;
	private $players = [];
	public function countPlayers()
	{
		return count($this->players);
	}

	public function addPlayer (Player $player) : void
	{
		if (count($this->players) === self::MAX_PLAYERS)
			throw new TooManyPlayersAtTheTableException(self::MAX_PLAYERS);
		$this->players[] = $player;
	}
}