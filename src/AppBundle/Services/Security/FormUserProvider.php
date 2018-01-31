<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-26
 * Time: 下午3:36
 */

namespace AppBundle\Services\Security;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FormUserProvider implements UserProviderInterface
{
    private $_em;

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * 根据username获取user对象
     *
     * @param string $username
     *
     * @return object|User|null
     */
    public function loadUserByUsername($username)
    {
        $user = $this->_em->getRepository('AppBundle:User')
            ->findOneBy(["userName"=>$username]);
        if(!$user){
            throw new UsernameNotFoundException();
        }
        return $user;
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }

}