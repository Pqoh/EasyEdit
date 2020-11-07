<?php

namespace platz1de\EasyEdit\selection;

use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Serializable;

abstract class Selection implements Serializable
{
	/**
	 * @var string
	 */
	protected $player;

	/**
	 * Selection constructor.
	 * @param string $player
	 */
	public function __construct(string $player)
	{
		$this->player = $player;
	}

	/**
	 * @return Vector3[]
	 */
	abstract public function getAffectedBlocks(): array;

	/**
	 * @param Vector3 $place
	 * @return Chunk[]
	 */
	abstract public function getNeededChunks(Vector3 $place): array;

	/**
	 * @return string
	 */
	public function getPlayer(): string
	{
		return $this->player;
	}

	public function close(): void
	{
	}
}