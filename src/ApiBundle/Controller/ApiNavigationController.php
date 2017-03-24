<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class ApiNavigationController extends FOSRestController
{
    /**
     * @Rest\Get("/current-position")
     */
    public function currentPosition()
    {
        $battle_manager = $this->container->get('app.battle_manager');
        $battle_manager->generateMonster();

        $navigation_manager = $this->container->get('app.navigation_manager');

        return $navigation_manager->generateUrls();
    }

    /**
     * @Rest\Get("/forward")
     */
    public function goForward()
    {
        $navigation_manager = $this->container->get('app.navigation_manager');
        return $navigation_manager->goForward();
    }

    /**
     * @Rest\Get("/back")
     */
    public function goBack()
    {
        $navigation_manager = $this->container->get('app.navigation_manager');
        return $navigation_manager->goBack();
    }

    /**
     * @Rest\Get("/left")
     */
    public function goLeft()
    {
        $navigation_manager = $this->container->get('app.navigation_manager');
        return $navigation_manager->goLeft();
    }

    /**
     * @Rest\Get("/right")
     */
    public function goRight()
    {
        $navigation_manager = $this->container->get('app.navigation_manager');
        return $navigation_manager->goRight();
    }
}