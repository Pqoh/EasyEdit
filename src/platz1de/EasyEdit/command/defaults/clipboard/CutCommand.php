<?php

namespace platz1de\EasyEdit\command\defaults\clipboard;

use platz1de\EasyEdit\command\EasyEditCommand;
use platz1de\EasyEdit\command\KnownPermissions;
use platz1de\EasyEdit\session\Session;
use platz1de\EasyEdit\task\editing\selection\CutTask;
use platz1de\EasyEdit\utils\ArgumentParser;

class CutCommand extends EasyEditCommand
{
	public function __construct()
	{
		parent::__construct("/cut", [KnownPermissions::PERMISSION_EDIT, KnownPermissions::PERMISSION_CLIPBOARD]);
	}

	/**
	 * @param Session               $session
	 * @param CommandFlagCollection $flags
	 */
	public function process(Session $session, CommandFlagCollection $flags): void
	{
		$session->runTask(new CutTask($session->getSelection(), $flags->getVectorFlag("relative")));
	}

	/**
	 * @param Session $session
	 * @return CommandFlag[]
	 */
	public function getKnownFlags(Session $session): array
	{
		return [
			"center" => new VectorValueCommandFlag("relative", $session->getSelection()->getBottomCenter(), [], "c"),
		];
	}

	/**
	 * @param CommandFlagCollection $flags
	 * @param Session               $session
	 * @param string[]              $args
	 * @return Generator<CommandFlag>
	 */
	public function parseArguments(CommandFlagCollection $flags, Session $session, array $args): Generator
	{
		if(!$flags->hasFlag("relative")){
			yield new VectorValueCommandFlag("relative", $session->asPlayer()->getPosition())
		}
	}
}