<?php

namespace ApiBundle\Controller;

use AppBundle\Event\MovementEvent;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiNavigationController extends FOSRestController
{
    /**
     * @Rest\Get("/movement/current-position")
     */
    public function currentPosition()
    {
        $navigation_manager = $this->container->get('app.navigation_manager');

        return $navigation_manager->generateUrls();
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

        if(!$battle_manager->userIsFighting()) {

            //monster spawn attempt
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('app.movement');

            $navigation_manager = $this->container->get('app.navigation_manager');

            return $navigation_manager->{'go'.ucfirst($direction)}();

        }

        return ['status' => 'in fight!'];
    }
}