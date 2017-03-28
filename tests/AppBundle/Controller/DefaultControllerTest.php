<?php
namespace Tests\AppBundle\Util;

use AppBundle\Manager\BattleManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use UserBundle\Entity\User;

class BattleManagerTest extends \PHPUnit_Framework_TestCase
{
    public function getMockUser()
    {
        $mock_user = $this->getMock(User::class, array(
            'getMonster'
        ));

        $mock_user->expects($this->once())
          ->method('getMonster')
          ->willReturn([]);

        return $mock_user;
    }

    public function getMockToken()
    {
        $mock_user = $this->getMockUser();

        $mock_post_auth_guard_token = $this->getMockBuilder(PostAuthenticationGuardToken::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();


        $mock_post_auth_guard_token->expects($this->once())
          ->method('getUser')
          ->willReturn($mock_user);

        return $mock_post_auth_guard_token;
    }

    public function getMockTokenStorageInterface()
    {
        $mock_token_storage = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getToken'])
            ->getMock();

        $mock_token_storage->expects($this->once())
          ->method('getToken')
          ->willReturn($this->getMockToken());

        return $mock_token_storage;
    }

    public function getMockEntityManager()
    {
        return $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * TEST
     */
    public function testSpawnMonster()
    {
        $mock_battle_manager = new BattleManager(
            $this->getMockTokenStorageInterface(),
            $this->getMockEntityManager()
        );

        $mock_battle_manager->spawnMonster();
    }
}