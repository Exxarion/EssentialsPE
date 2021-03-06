<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Depth extends BaseCommand{
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "depth", "Display your depth related to sea-level", "/depth", ["height"]);
        $this->setPermission("essentials.depth");
    }

    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Please run this command in-game");
            return false;
        }
        if(count($args) !== 0){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        $pos = $sender->getFloorY() - 63;
        if($pos === 0){
            $sender->sendMessage(TextFormat::AQUA . "You're at sea level");
        }else{
            $sender->sendMessage(TextFormat::AQUA . "You're " . (substr($pos, 0, 1) === "-" ? substr($pos, 1) : $pos) . " meters " . ($pos > 0 ? "above" : "below") . " the sea.");
        }
        return true;
    }
}