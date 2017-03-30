<?php

namespace ApiBundle\Controller;

use AppBundle\Manager\BattleManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiNavigationController extends FOSRestController
{
    /**
     * @Rest\Get("/movement/current-position")
     *
     */
    public function currentPosition()
    {
        $navigation_manager = $this->container->get('app.navigation_manager');
        $player_manager = $this->container->get('app.player_manager');

        return [
            'player_profile' => $player_manager->getPlayerProfile(),
            'navigation' => $navigation_manager->generateUrls()
        ];

    }

    /**
     * @Rest\Get("/movement/{direction}")
     *
     * @param $direction
     * @return mixed
     */
    public function movement($direction)
    {
        $battle_manager = $this->container->get('app.battle_manager');

        //you are dead. Sorry
        if($battle_manager->youAreDead()) {
            return [
                'player_status' => BattleManager::DEAD,
                'status_description' => 'You are dead'
            ];
        }

        //you are fighting. You can't move!
        if($battle_manager->userIsFighting()) {
            return [
                'player_status' => BattleManager::BATTLE_IN_FIGHT_STATUS,
                'status_description' => 'A monster appear! You cannot move. Fight (/api/battle/fight) or try to escape (/api/battle/escape)!'
            ];
        }

        //monster spawn attempt
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('app.movement');

        $navigation_manager = $this->container->get('app.navigation_manager');
        $player_manager = $this->container->get('app.player_manager');

        return [
            'player_profile' => $player_manager->getPlayerProfile(),
            'navigation' => $navigation_manager->{'go'.ucfirst($direction)}(),
        ];
    }
}