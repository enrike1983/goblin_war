<?php

namespace AppBundle\EventListener;

use AppBundle\Manager\BattleManager;

class NavigationListener {

    protected $battle_manager;

    public function __construct(BattleManager $battleManager)
    {
        $this->battle_manager = $battleManager;
    }

    public function onMovementEvent()
    {
        $this->battle_manager->spawnMonster();

        /*$bid = $event->getBid();

        $message = \Swift_Message::newInstance()
            ->setSubject('New bid posted')
            ->setFrom('noreply@example.com','Example')
            ->setTo($bid->getOwner()->getEmail())
            ->setBody(
                $this->engine->render(
                    'App:Mail:newBid.html.twig',
                    array(
                        'bid' => $bid
                    )
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);*/
    }
}
