<?php
namespace tests\AppBundle\Manager;

use AppBundle\Manager\BattleManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use UserBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Monster;


class BattleManagerTest extends TestCase
{
    /**
     * User Mock
     *
     * @param bool $user_has_monster
     * @return mixed
     */
    public function getMockUser($user_has_monster = true)
    {
        $mock_monster = $this->getMockBuilder(Monster::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock_user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMonster'])
            ->getMock();

        if($user_has_monster) {
            $mock_user->expects($this->once())
              ->method('getMonster')
              ->willReturn($mock_monster);
        } else {
            $mock_user->expects($this->once())
              ->method('getMonster')
              ->willReturn([]);
        }

        return $mock_user;
    }

    /**
     * Token Mock
     *
     * @param bool $user_has_monster
     * @return mixed
     */
    public function getMockToken($user_has_monster = true)
    {
        $mock_user = $this->getMockUser($user_has_monster);

        $mock_post_auth_guard_token = $this->getMockBuilder(PostAuthenticationGuardToken::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();


        $mock_post_auth_guard_token->expects($this->once())
          ->method('getUser')
          ->willReturn($mock_user);

        return $mock_post_auth_guard_token;
    }

    /**
     * Token Storage Mock
     *
     * @param bool $user_has_monster
     * @return mixed
     */
    public function getMockTokenStorageInterface($user_has_monster = true)
    {
        $mock_token_storage = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getToken'])
            ->getMock();

        $mock_token_storage->expects($this->once())
          ->method('getToken')
          ->willReturn($this->getMockToken($user_has_monster));

        return $mock_token_storage;
    }

    /**
     * Entity Manager Mock
     *
     * @return mixed
     */
    public function getMockEntityManager()
    {
        $mock_entity_manager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'flush'])
            ->getMock();

        $mock_entity_manager->expects($this->any())
          ->method('flush');

        $mock_entity_manager->expects($this->any())
          ->method('persist');


        return $mock_entity_manager;
    }

    /**
     * Monster Exists test
     */
    public function testSpawnMonsterExistsMonster()
    {
        $mock_battle_manager = new BattleManager(
            $this->getMockTokenStorageInterface(),
            $this->getMockEntityManager()
        );

        $monster = $mock_battle_manager->spawnMonster();

        $this->assertContains('getId', get_class_methods($monster));
    }

    /**
     * Monster does not exists
     */
    public function testSpawnMonsterDoesNotExistsMonster()
    {
        $mock_battle_manager = new BattleManager(
            $this->getMockTokenStorageInterface(false),
            $this->getMockEntityManager()
        );

        mt_srand(56);

        $monster = $mock_battle_manager->spawnMonster();

        $this->assertContains('getId', get_class_methods($monster));
    }
}