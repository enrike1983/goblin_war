<?php

namespace ApiBundle\Controller;

use AppBundle\Manager\BattleManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiBattleController extends FOSRestController
{
    /**
     * @Rest\Get("/battle/fight")
     */
    public function fight()
    {
        $navigation_manager = $this->container->get('app.battle_manager');
        $fight_result = $navigation_manager->doFight();

        die(var_dump($fight_result));
    }
}