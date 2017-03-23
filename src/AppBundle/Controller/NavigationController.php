<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class NavigationController extends FOSRestController
{
    /**
     * @Rest\Get("/forward")
     */
    public function goForward()
    {
        return ['ciccio', 'pasticcio'];
    }
}