<?php

declare(strict_types=1);

namespace mm\entities;

use pocketmine\block\BlockTypeIds;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use mm\utils\ColorUtils;
use function array_filter;
use function count;

class NpcEntity extends Human {

    public function __construct(Location $location, Skin $skin, CompoundTag $nbt) {
        parent::__construct($location, $skin);
    }

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);

        $this->updateNameTag();
        $this->setNameTagAlwaysVisible();
    }

    public function updateNameTag(): void {
        $amount = $this->getSessionsCount();
        $this->setNameTag(ColorUtils::translate(
            "{YELLOW}{BOLD}CLICK TO PLAY{RESET}\n" .
            "{YELLOW}{BOLD}MURDER MYSTERY{RESET}\n" .
            "{YELLOW}{BOLD}CLASSIC\n" . 
            "{YELLOW}{BOLD}PLAYERS: ?"
        ));
    }

    public function attack(EntityDamageEvent $source): void {
        if($source instanceof EntityDamageByChildEntityEvent or !$source instanceof EntityDamageByEntityEvent) {
            return;
        }
        $damager = $source->getDamager();
        if(!$damager instanceof Player) {
            return;
        }

        if($damager->hasPermission(DefaultPermissions::ROOT_OPERATOR) and
            $damager->getInventory()->getItemInHand()->getTypeId() === BlockTypeIds::BEDROCK) {
            $this->kill();
            return;
        }

        Server::getInstance()->getCommandMap()->dispatch($player, "mm join");
    }
}