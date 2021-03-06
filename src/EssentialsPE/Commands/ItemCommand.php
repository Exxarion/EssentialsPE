<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ItemCommand extends BaseCommand{
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "item", "Gives yourself an item", "/item <item[:damage]> [amount]", ["i"]);
        $this->setPermission("essentials.item");
    }

    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Please run this command in-game");
            return false;
        }
        if(($gm = $sender->getServer()->getGamemodeString($sender->getGamemode())) === "CREATIVE" || $gm === "SPECTATOR"){
            $sender->sendMessage(TextFormat::RED . "[Error] You're in " . strtolower($gm) . " mode");
            return false;
        }
        if(count($args) < 1 || count($args) > 2){
            $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
            return false;
        }

        //Getting the item...
        $item_name = array_shift($args);
        if(strpos($item_name, ":") !== false){
            $v = explode(":", $item_name);
            $item_name = $v[0];
            $damage = $v[1];
        }else{
            $damage = 0;
        }

        if(!is_numeric($item_name)){
            $item = Item::fromString($item_name);
        }else{
            $item = Item::get($item_name);
        }
        $item->setDamage($damage);

        if($item->getID() === 0){
            $sender->sendMessage(TextFormat::RED . "Unknown item \"" . $item_name . "\"");
            return false;
        }elseif(!$sender->hasPermission("essentials.itemspawn.item-all") || !$sender->hasPermission("essentials.itemspawn.item-" . $item->getName() || !$sender->hasPermission("essentials.itemspawn.item-" . $item->getID()))){
            $sender->sendMessage(TextFormat::RED . "You can't spawn this item");
            return false;
        }

        //Setting the amount...
        $amount = array_shift($args);
        if(!isset($amount) || !is_numeric($amount)){
            if(!$sender->hasPermission("essentials.oversizedstacks")){
                $item->setCount($item->getMaxStackSize());
            }else{
                $item->setCount($this->getPlugin()->getConfig()->get("oversized-stacks"));
            }
        }else{
            $item->setCount($amount);
        }

        //Getting other values...
        /*foreach($args as $a){
            //Example
            if(stripos(strtolower($a), "color") !== false){
                $v = explode(":", $a);
                $color = $v[1];
            }
        }*/

        //Giving the item...
        $slot = $sender->getInventory()->firstEmpty();
        $sender->getInventory()->setItem($slot, $item);
        $sender->sendMessage(TextFormat::YELLOW . "Giving " . TextFormat::RED . $item->getCount() . TextFormat::YELLOW . " of " . TextFormat::RED . ($item->getName() === "Unknown" ? $item_name : $item->getName()));
        return false;
    }
}
