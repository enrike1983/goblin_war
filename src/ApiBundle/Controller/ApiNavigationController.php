<?php

namespace ApiBundle\Controller;

use AppBundle\Manager\BattleManager;

//use FOS\RestBundle\Controller\Annotations as Rest;
use ApiBundle\Controller\BaseController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiNavigationController extends BaseController
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

        //you are dead. Sorry
        if($battle_manager->youAreDead()) {

            //response
            return $this->getGwResponse(
                BattleManager::PLAYER_IS_DEAD,
                $player_manager->getPlayerProfile(),
                $navigation_manager->generateUrls(),
                self::MESSAGE_YOU_ARE_DEAD,
                Response::HTTP_OK
            );
        }

        //you are fighting. You can't move!
        if($battle_manager->userIsFighting()) {

            //response
            return $this->getGwResponse(
                BattleManager::BATTLE_IN_FIGHT_STATUS,
                $player_manager->getPlayerProfile(),
                $navigation_manager->generateUrls(),
                self::MESSAGE_MONSTER_APPEAR,
                Response::HTTP_OK
            );
        }

        return $this->getGwResponse(
            BattleManager::PLAYER_IS_MOVING,
            $player_manager->getPlayerProfile(),
            $navigation_manager->generateUrls(),
            self::MESSAGE_MOVING,
            Response::HTTP_OK
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
        $navigation_manager = $this->container->get('app.navigation_manager');
        $player_manager = $this->container->get('app.player_manager');
        $battle_manager = $this->container->get('app.battle_manager');

        //you are dead. Sorry
        if($battle_manager->youAreDead()) {
            return $this->getGwResponse(
                BattleManager::PLAYER_IS_DEAD,
                $player_manager->getPlayerProfile(),
                $navigation_manager->generateUrls(),
                self::MESSAGE_YOU_ARE_DEAD,
                Response::HTTP_OK
            );
        }

        //you are fighting. You can't move!
        if($battle_manager->userIsFighting()) {
            return $this->getGwResponse(
                BattleManager::BATTLE_IN_FIGHT_STATUS,
                $player_manager->getPlayerProfile(),
                $navigation_manager->generateUrls(),
                self::MESSAGE_MONSTER_APPEAR,
                Response::HTTP_OK
            );
        }

        //monster spawn attempt
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('app.movement');

        return $this->getGwResponse(
            BattleManager::PLAYER_IS_MOVING,
            $player_manager->getPlayerProfile(),
            $navigation_manager->{'go'.ucfirst($direction)}(),
            self::MESSAGE_MOVING,
            Response::HTTP_OK
        );
    }
}