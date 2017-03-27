<?php
namespace AppBundle\Manager;

use AppBundle\Entity\Monster;
use Doctrine\ORM\EntityManager;

use SensioLabs\Security\SecurityChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BattleManager
{
    const BATTLE_IN_FIGHT_STATUS = 1;
    const BATTLE_USER_WINS = 2;
    const BATTLE_USER_LOSES = 3;
    const DAMAGE = 5;

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
     * Generates a random monster with random skills.
     * When a monster is generated it's created in db and associated to the user
     */
    public function spawnMonster()
    {
        //exists already a monster for this user?
        $monster = $this->user->getMonster();

        if ($monster) {
            return $monster;
        }

        //with a certain probability generates a monster. For now probability = 50% :)
        $rand = mt_rand(0, 100);

        //15% probability
        if ($rand <= 15) {
            $new_monster = new Monster();
            $new_monster->setAttack(mt_rand(Monster::MIN_ATTACK, Monster::MAX_ATTACK));
            $new_monster->setUser($this->user);

            $generator = \Nubs\RandomNameGenerator\All::create();
            $new_monster->setName($generator->getName());

            $this->entity_manager->persist($new_monster);
            $this->entity_manager->flush();

            return $new_monster;
        }

        return false;
    }

    /**
     * Fights the monster ( greater attack value wins. If player looses life -5 )
     */
    public function doFight()
    {
        $monster = $this->userIsFighting();

        if($monster) {

            $result = $this->user->getAttack() - $monster->getAttack();

            //remove monster
            $this->entity_manager->remove($monster);
            $this->entity_manager->flush();

            //player wins
            if($result > 0) {

                //here we raise up the experience

                return self::BATTLE_USER_WINS;
            }

            //remove user's life
            $this->user->setLife($this->user->getLife() - self::DAMAGE);
            $this->entity_manager->flush();
            $this->entity_manager->remove($monster);
            $this->entity_manager->flush();

            return self::BATTLE_USER_LOSES;
        }

        throw new \BadMethodCallException(__METHOD__.' call not allowed');
    }

    /**
     * Try to escape. If escapes no damage, otherwise the monster damages.
     */
    public function doEscape()
    {
    }

    /**
     * Checks if the current user is still fighting ( has a monster associated )
     */
    public function userIsFighting()
    {
        return $this->user->getMonster();
    }

    /**
     * Check if you are dead :D
     */
    protected function youAreDead()
    {
    }

    /**
     * Raise up experience when a monster is killed!
     */
    protected function raiseUpExperience()
    {

    }
}