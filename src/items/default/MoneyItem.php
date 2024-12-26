<?php
declare(strict_types=1);

namespace arkania\items\default;

use arkania\items\components\AllowOffHandComponent;
use arkania\items\components\CanDestroyInCreativeComponent;
use arkania\items\components\HandEquippedComponent;
use arkania\items\components\MaxStackSizeComponent;
use arkania\items\ItemBase;
use arkania\Main;
use arkania\session\Session;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class MoneyItem extends ItemBase {

    private int $amount;

    public function getComponents() : array {
        return [
            new MaxStackSizeComponent(64),
            new CanDestroyInCreativeComponent(true),
            new AllowOffHandComponent(false),
            new HandEquippedComponent(false)
        ];
    }

    public function setAmount(int $amount) : Item {
        $this->amount = $amount;
        return $this;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult {
        if(!isset($this->amount)) {
            return ItemUseResult::NONE();
        }
        $session = Session::get($player);
        if($player->isSneaking()) {
            Main::getInstance()->getEconomyManager()->addMoney($player->getName(), $this->amount * $player->getInventory()->getItemInHand()->getCount())->then(function () use ($player, $session) {
                if($session->getCumulative()['time'] - time() >= 0) {
                    $session->setCumulatif([
                        'amount' => $session->getCumulative()['amount'] + $this->amount * $player->getInventory()->getItemInHand()->getCount(),
                        'time' => time() + 5
                    ]);
                    $player->sendActionBarMessage('§a+' . $session->getCumulative()['amount'] . '$');
                }else{
                    $player->sendActionBarMessage('§a+' . $this->amount * $player->getInventory()->getItemInHand()->getCount() . '$');
                }
                $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount(0));
            });
        } else {
            Main::getInstance()->getEconomyManager()->addMoney($player->getName(), $this->amount)->then(function () use ($player, $session) {
                if($session->getCumulative()['time'] - time() >= 0) {
                    $session->setCumulatif([
                        'amount' => $session->getCumulative()['amount'] + $this->amount,
                        'time' => time() + 5
                    ]);
                    $player->sendActionBarMessage('§a+' . $session->getCumulative()['amount'] . '$');
                }else{
                    $player->sendActionBarMessage('§a+' . $this->amount . '$');
                }
                $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
            });
        }
        return ItemUseResult::NONE();
    }

    protected function deserializeCompoundTag(CompoundTag $tag) : void {
        $this->amount = $tag->getInt("amount");
        parent::deserializeCompoundTag($tag);
    }

    protected function serializeCompoundTag(CompoundTag $tag) : void {
        $tag->setInt("amount", $this->amount ?? 0);
        parent::serializeCompoundTag($tag);
    }


}