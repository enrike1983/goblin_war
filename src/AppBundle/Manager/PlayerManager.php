<?php
namespace AppBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PlayerManager
{
    protected $token_storage;
    protected $user;
    protected $entity_manager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->token_storage = $tokenStorage;
        $this->entity_manager = $entityManager;

        $this->user = $this->token_storage->getToken()->getUser();
    }
    /**
     * Return a structured array with the player's profile ( name, life, attack )
     */
    public function getPlayerProfile()
    {
        return [
            'name' => $this->user->getUsername(),
            'attack' => $this->user->getAttack(),
            'life' => $this->user->getLife(),
        ];
    }
}