<?php

namespace platz1de\EasyEdit\command\defaults;

use platz1de\EasyEdit\brush\BrushHandler;
use platz1de\EasyEdit\command\EasyEditCommand;
use platz1de\EasyEdit\pattern\ParseError;
use platz1de\EasyEdit\pattern\PatternParser;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class BrushCommand extends EasyEditCommand
{
	public function __construct()
	{
		parent::__construct("/brush", "Create a new Brush", "easyedit.command.brush", "//brush sphere [radius] [pattern]\n//brush smooth [radius]\n//brush naturalize [radius] [topBlock] [middleBlock] [bottomBlock]\n//brush cylinder [radius] [height] [pattern]", ["/br"]);
	}

	/**
	 * @param Player   $player
	 * @param string[] $args
	 */
	public function process(Player $player, array $args): void
	{
		$type = BrushHandler::nameToIdentifier($args[0] ?? "");

		$nbt = CompoundTag::create()->setString("brushType", BrushHandler::identifierToName($type));
		switch ($type) {
			case BrushHandler::BRUSH_SPHERE:
				try {
					PatternParser::parse($args[2] ?? "stone");
				} catch (ParseError $exception) {
					$player->sendMessage($exception->getMessage());
					return;
				}
				$nbt->setShort("brushSize", (int) ($args[1] ?? 3));
				$nbt->setString("brushPattern", $args[2] ?? "stone");
				break;
			case BrushHandler::BRUSH_SMOOTH:
				$nbt->setShort("brushSize", (int) ($args[1] ?? 5));
				break;
			case BrushHandler::BRUSH_NATURALIZE:
				try {
					PatternParser::parse($args[2] ?? "grass");
					PatternParser::parse($args[3] ?? "dirt");
					PatternParser::parse($args[4] ?? "stone");
				} catch (ParseError $exception) {
					$player->sendMessage($exception->getMessage());
					return;
				}
				$nbt->setShort("brushSize", (int) ($args[1] ?? 4));
				$nbt->setString("topBlock", $args[2] ?? "grass");
				$nbt->setString("middleBlock", $args[3] ?? "dirt");
				$nbt->setString("bottomBlock", $args[4] ?? "stone");
				break;
			case BrushHandler::BRUSH_CYLINDER:
				try {
					PatternParser::parse($args[3] ?? "stone");
				} catch (ParseError $exception) {
					$player->sendMessage($exception->getMessage());
					return;
				}
				$nbt->setShort("brushSize", (int) ($args[1] ?? 4));
				$nbt->setShort("brushHeight", (int) ($args[2] ?? 2));
				$nbt->setString("brushPattern", $args[3] ?? "stone");
		}
		$item = VanillaItems::WOODEN_SHOVEL()->setNamedTag($nbt);
		$lore = [];
		foreach ($nbt->getValue() as $name => $value) {
			$lore[] = $name . ": " . $value;
		}
		$item->setLore($lore);
		$item->setCustomName(TextFormat::GOLD . "Brush");
		$player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), $item);
	}
}