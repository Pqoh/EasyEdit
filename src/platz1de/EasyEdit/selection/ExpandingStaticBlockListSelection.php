<?php

namespace platz1de\EasyEdit\selection;

use platz1de\EasyEdit\utils\VectorUtils;
use pocketmine\math\Vector3;

class ExpandingStaticBlockListSelection extends StaticBlockListSelection
{
	/**
	 * @param string       $player
	 * @param string       $world
	 * @param Vector3|null $pos
	 */
	public function __construct(string $player, string $world = "", Vector3 $pos = null)
	{
		parent::__construct($player, $world, $pos, $pos);
		if ($pos !== null) {
			$this->getManager()->loadIfNeeded($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
		}
	}

	public function addBlock(int $x, int $y, int $z, int $id, bool $overwrite = true): void
	{
		VectorUtils::adjustBoundaries($x, $y, $z, $this->pos1, $this->pos2);
		$this->getManager()->loadIfNeeded($x >> 4, $z >> 4);
		parent::addBlock($x, $y, $z, $id, $overwrite);
	}

	//NOTE: this selection is split into static block lists
}