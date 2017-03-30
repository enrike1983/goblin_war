<?php

namespace ApiBundle\Controller;

use AppBundle\Manager\BattleManager;
use FOS\RestBundle\Controller\FOSRestController;
//use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiNavigationController extends FOSRestController
{
    /**
     * @Route("/movement/current-position")
     * @Method({"GET", "OPTIONS"})
     */
    public function currentPosition()
    {
        $navigation_manager = $this->container->get('app.navigation_manager');
        $player_manager = $this->container->get('app.player_manager');
        $battle_manager = $this->container->get('app.battle_manager');

        $fight_info_array = array();
        $player_status = null;

        //you are fighting. You can't move!
        if($battle_manager->userIsFighting()) {

            $player_status = BattleManager::BATTLE_IN_FIGHT_STATUS;

            $fight_info_array = [
                'status_description' => 'A monster appear! You cannot move. Fight (/api/battle/fight) or try to escape (/api/battle/escape)!',
            ];
        }

        return array_merge(
            $fight_info_array, [
                'player_status' => $player_status ?: BattleManager::PLAYER_IS_MOVING,
                'player_profile' => $player_manager->getPlayerProfile(),
                'navigation' => $navigation_manager->generateUrls()
            ]
        );

    }

    /**
     * @Route("/movement/{direction}")
     * @Method({"GET", "OPTIONS"})
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
                'player_status' => BattleManager::PLAYER_IS_DEAD,
                'status_description' => 'You are dead',
            ];
        }

        //you are fighting. You can't move!
        if($battle_manager->userIsFighting()) {
            return [
                'player_status' => BattleManager::BATTLE_IN_FIGHT_STATUS,
                'status_description' => 'A monster appear! You cannot move. Fight or try to escape!',
            ];
        }

        //monster spawn attempt
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('app.movement');

        $navigation_manager = $this->container->get('app.navigation_manager');
        $player_manager = $this->container->get('app.player_manager');

        return [
            'player_status' => BattleManager::PLAYER_IS_MOVING,
            'player_profile' => $player_manager->getPlayerProfile(),
            'navigation' => $navigation_manager->{'go'.ucfirst($direction)}(),
        ];
    }
}