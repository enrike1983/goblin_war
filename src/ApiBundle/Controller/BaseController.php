<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

abstract class BaseController extends FOSRestController
{
    const MESSAGE_MONSTER_APPEAR = 'A monster appear! You cannot move. Fight (/api/battle/fight) or try to escape (/api/battle/escape)!';
    const MESSAGE_YOU_ARE_DEAD = 'You are dead';
    const MESSAGE_MOVING = 'You are moving...';

    /**
     * Returns the Goblin War custom response
     *
     * @param $player_status
     * @param $player_profile
     * @param $navigation
     * @param $message
     * @param $status_code
     * @return mixed
     */
    public function getGwResponse($player_status, $player_profile, $navigation, $message, $status_code)
    {
        return View::create([
            'player_status' => $player_status,
            'message' => $message,
            'navigation' => $navigation,
            'player_profile' => $player_profile
        ], $status_code);
    }
}