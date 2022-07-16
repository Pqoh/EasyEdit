<?php

namespace platz1de\EasyEdit\selection;

use Closure;
use platz1de\EasyEdit\selection\constructor\CubicConstructor;
use platz1de\EasyEdit\selection\cubic\CubicChunkLoader;
use platz1de\EasyEdit\utils\VectorUtils;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use UnexpectedValueException;

class Cube extends Selection implements Patterned
{
	use CubicChunkLoader;

	/**
	 * splits into 3x3 Chunk pieces
	 * @param Vector3 $offset
	 * @return Cube[]
	 */
	public function split(Vector3 $offset): array
	{
		if ($this->piece) {
			throw new UnexpectedValueException("Pieces are not split able");
		}

		$min = VectorUtils::enforceHeight($this->pos1->addVector($offset));
		$max = VectorUtils::enforceHeight($this->pos2->addVector($offset));

		$pieces = [];
		for ($x = $min->getX() >> 4; $x <= $max->getX() >> 4; $x += 3) {
			for ($z = $min->getZ() >> 4; $z <= $max->getZ() >> 4; $z += 3) {
				$pieces[] = new Cube($this->getWorldName(), new Vector3(max(($x << 4) - $offset->getX(), $this->pos1->getX()), $this->pos1->getY(), max(($z << 4) - $offset->getZ(), $this->pos1->getZ())), new Vector3(min((($x + 2) << 4) + 15 - $offset->getX(), $this->pos2->getX()), $this->pos2->getY(), min((($z + 2) << 4) + 15 - $offset->getZ(), $this->pos2->getZ())), true);
			}
		}
		return $pieces;
	}

	/**
	 * @param Closure          $closure
	 * @param SelectionContext $context
	 * @param Selection        $full
	 */
	public function useOnBlocks(Closure $closure, SelectionContext $context, Selection $full): void
	{
		if ($context->isEmpty()) {
			return;
		}

		if ($context->isFull()) {
			CubicConstructor::betweenPoints($this->getPos1(), $this->getPos2(), $closure);
			return;
		}

		if ($context->includesFilling()) {
			//This can also make the selection larger (1x1 -> -3x-3), so we are not allowed to actually check for the smaller/larger position
			CubicConstructor::betweenPoints(Vector3::maxComponents($full->getCubicStart()->add(1, 1, 1), $this->getCubicStart()), Vector3::minComponents($full->getCubicEnd()->subtract(1, 1, 1), $this->getCubicEnd()), $closure);
		}

		if ($context->includesAllSides()) {
			CubicConstructor::onSides($this->getPos1(), $this->getPos2(), Facing::ALL, $context->getSideThickness(), $closure);
		} elseif ($context->includesWalls()) {
			CubicConstructor::onSides($this->getPos1(), $this->getPos2(), Facing::HORIZONTAL, $context->getSideThickness(), $closure);
		}

		if ($context->includesCenter()) {
			CubicConstructor::betweenPoints(Vector3::maxComponents($full->getPos1()->addVector($full->getPos2())->divide(2)->floor(), $this->getPos1()), Vector3::minComponents($full->getPos1()->addVector($full->getPos2())->divide(2)->ceil(), $this->getPos2()), $closure);
		}
	}
}