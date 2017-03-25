<?php
namespace AppBundle\Manager;

use AppBundle\Event\MovementEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NavigationManager
{
    const INITIAL_POSITION = [1,1];
    const FORWARD_KEY = 'forward';
    const RIGHT_KEY = 'right';
    const BACK_KEY = 'back';
    const LEFT_KEY = 'left';
    const CURRENT_KEY = 'current';

    const ROW_KEY = 0;
    const COLUMN_KEY = 1;

    protected $dungeon_map;
    protected $token_storage;
    protected $user_position;
    protected $user;
    protected $entity_manager;
    protected $event_dispatcher;

    public function __construct(array $dungeonMap, TokenStorageInterface $tokenStorage, EntityManager $entityManager, TraceableEventDispatcher $eventDispatcher)
    {
        $this->dungeon_map = $dungeonMap;
        $this->token_storage = $tokenStorage;
        $this->entity_manager = $entityManager;
        $this->event_dispatcher = $eventDispatcher;

        $this->user = $this->token_storage->getToken()->getUser();
        $this->user_position = $this->user->getPosition();
    }

    /**
     * Generates all the urls depending on the current user position
     */
    public function generateUrls()
    {
        $url_array[self::FORWARD_KEY] = $this->getForwardRoom();
        $url_array[self::LEFT_KEY] = $this->getLeftRoom();
        $url_array[self::RIGHT_KEY] = $this->getRightRoom();
        $url_array[self::BACK_KEY] = $this->getBackRoom();
        $url_array[self::CURRENT_KEY] = $this->getCurrentRoom();

        return $url_array;
    }

    /**
     * Persists current user position in db
     */
    public function persistUserPosition()
    {
        $this->user->setPosition($this->user_position);

        $this->entity_manager->persist($this->user);
        $this->entity_manager->flush();
    }

    /**
     * Get the current room
     *
     * @return bool
     */
    public function getCurrentRoom()
    {
        return $this->dungeon_map[$this->user_position[self::ROW_KEY]][$this->user_position[self::COLUMN_KEY]];
    }

    /**
     * Get the forward room if exists, false otherwise
     *
     * @return bool
     */
    public function getForwardRoom()
    {
        if(isset($this->dungeon_map[$this->user_position[self::ROW_KEY] - 1][$this->user_position[self::COLUMN_KEY]])) {
            return $this->dungeon_map[$this->user_position[self::ROW_KEY] - 1][$this->user_position[self::COLUMN_KEY]];
        } else {
            return false;
        }
    }

    /**
     * Get the left room if exists, false otherwise
     *
     * @return bool
     */
    public function getLeftRoom()
    {
        if(isset($this->dungeon_map[$this->user_position[self::ROW_KEY]][$this->user_position[self::COLUMN_KEY] - 1])) {
            return $this->dungeon_map[$this->user_position[self::ROW_KEY]][$this->user_position[self::COLUMN_KEY] - 1];
        } else {
            return false;
        }
    }

    /**
     * Get the right room if exists, false otherwise
     *
     * @return bool
     */
    public function getRightRoom()
    {
        if(isset($this->dungeon_map[$this->user_position[self::ROW_KEY]][$this->user_position[self::COLUMN_KEY] + 1])) {
            return $this->dungeon_map[$this->user_position[self::ROW_KEY]][$this->user_position[self::COLUMN_KEY] +1];
        } else {
            return false;
        }
    }

    /**
     * Get the right room if exists, false otherwise
     *
     * @return bool
     */
    public function getBackRoom()
    {
        if(isset($this->dungeon_map[$this->user_position[self::ROW_KEY] + 1][$this->user_position[self::COLUMN_KEY]])) {
            return $this->dungeon_map[$this->user_position[self::ROW_KEY] + 1][$this->user_position[self::COLUMN_KEY]];
        } else {
            return false;
        }
    }

    /**
     * Verifies the availability to go forward and moves the user current position
     */
    public function goForward()
    {
        if($this->getForwardRoom()) {
            $this->user_position[self::ROW_KEY]--;

            $this->persistUserPosition();
        }

        return $this->generateUrls();
    }

    /**
     * Verifies the availability to go back and moves the user current position
     */
    public function goBack()
    {
        if($this->getBackRoom()) {
            $this->user_position[self::ROW_KEY]++;

            $this->persistUserPosition();
        }

        return $this->generateUrls();
    }

    /**
     * Verifies the availability to go right and moves the user current position
     */
    public function goRight()
    {
        if($this->getRightRoom()) {
            $this->user_position[self::COLUMN_KEY]++;

            $this->persistUserPosition();
        }

        return $this->generateUrls();
    }

    /**
     * Verifies the availability to go right and moves the user current position
     */
    public function goLeft()
    {
        if($this->getLeftRoom()) {
            $this->user_position[self::COLUMN_KEY]--;

            $this->persistUserPosition();
        }

        return $this->generateUrls();
    }
}