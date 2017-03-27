<?php

namespace AppBundle\EventListener;

use AppBundle\Manager\BattleManager;

class NavigationListener {

    protected $battle_manager;

    public function __construct(BattleManager $battleManager)
    {
        $this->battle_manager = $battleManager;
    }

    public function onMovementEvent()
    {
        $this->battle_manager->spawnMonster();
    }
}
