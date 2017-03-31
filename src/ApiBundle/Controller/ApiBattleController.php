<?php

namespace ApiBundle\Controller;

use AppBundle\Manager\BattleManager;
//use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ApiBundle\Controller\BaseController as BaseController;

class ApiBattleController extends BaseController
{
    const MESSAGE_WINNER = 'Winner!';
    const MESSAGE_LOSER = 'Loser :(';
    const MESSAGE_ESCAPE_SUCCESS = 'Escape successful';
    const MESSAGE_ESCAPE_FAIL = 'Escape fail :(';

    /**
     * @Route("/battle/fight")
     * @Method({"GET", "OPTIONS"})
     */
    public function fight()
    {
        $navigation_manager = $this->container->get('app.battle_manager');
        $player_manager = $this->container->get('app.player_manager');

        try {
            $fight_result = $navigation_manager->doFight();

            $fight_message = self::MESSAGE_LOSER;
            if($fight_result === BattleManager::BATTLE_USER_WINS) {
                $fight_message = self::MESSAGE_WINNER;
            }
            $status_code = Response::HTTP_OK;
        } catch (\BadMethodCallException $e) {
            $fight_result = BattleManager::BATTLE_ERROR;
            $fight_message = $e->getMessage();
            $status_code = Response::HTTP_BAD_REQUEST;
        }

        //response
        return $this->getGwResponse(
            $fight_result,
            $player_manager->getPlayerProfile(),
            $navigation_manager->generateUrls(),
            $fight_result,
            $status_code
        );
    }

    /**
     * @Route("/battle/escape")
     * @Method({"GET", "OPTIONS"})
     */
    public function escape()
    {
        $navigation_manager = $this->container->get('app.battle_manager');
        $player_manager = $this->container->get('app.player_manager');

        try {
            $fight_result = $navigation_manager->doEscape();

            $fight_message = self::MESSAGE_ESCAPE_FAIL;
            if ($fight_result === BattleManager::ESCAPE_SUCCESS) {
                $fight_message = self::MESSAGE_ESCAPE_SUCCESS;
            }
            $status_code = Response::HTTP_OK;
        } catch (\BadMethodCallException $e) {
            $fight_result = BattleManager::BATTLE_ERROR;
            $fight_message = $e->getMessage();
            $status_code = Response::HTTP_BAD_REQUEST;
        }

        //response
        return $this->getGwResponse(
            $fight_result,
            $player_manager->getPlayerProfile(),
            $navigation_manager->generateUrls(),
            $fight_result,
            $status_code
        );
    }
}