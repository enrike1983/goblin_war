<?php

namespace ApiBundle\Controller;

use AppBundle\Manager\BattleManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
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

        try {
            $fight_result = $navigation_manager->doFight();
            $status_code = Response::HTTP_OK;
        } catch (\BadMethodCallException $e) {
            $fight_result = $e->getMessage();
            $status_code = Response::HTTP_BAD_REQUEST;
        }

        return View::create($fight_result, $status_code);
    }
}