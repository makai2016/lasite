<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-28
 * Time: 下午8:33
 */

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Query\Expr\Join;

class AppExtension extends \Twig_Extension
{
    private $_container;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    private function get($id)
    {
        return $this->_container->get($id);
    }

    /**
     * twig函数的扩展
     * @return array
     */
    public function getFunctions()
    {
        return [
            'parameter'=> new \Twig_Function_Method($this, 'parameter'),
            'after'=> new \Twig_Function_Method($this, 'after'),
            'prevArticle'=> new \Twig_Function_Method($this, 'prevArticle'),
            'nextArticle'=> new \Twig_Function_Method($this, 'nextArticle'),
            'prevCase'=> new \Twig_Function_Method($this, 'prevCase'),
            'nextCase'=> new \Twig_Function_Method($this, 'nextCase'),
        ];
    }

    public function parameter($parameter)
    {
        return $this->_container->getParameter($parameter);
    }


    public function after($createdAt)
    {
        $date = new \DateTime();
        $interval = $date->diff($createdAt);
        if($interval->y){
            return $interval->y.'年前';
        }
        if($interval->m){
            return $interval->m.'月前';
        }
        if($interval->d){
            return $interval->d.'天前';
        }
        if($interval->h){
            return $interval->h.'小时前';
        }
        if($interval->i){
            return $interval->i.'分钟前';
        }
        if($interval->s){
            return $interval->s.'秒前';
        }
    }


    public function prevArticle($id,$categoryId)
    {
        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('a')->from('AppBundle:Article','a')
            ->where("a.id<{$id}")
            ->andWhere("a.categoryId={$categoryId}")
            ->setMaxResults(1)->orderBy('a.id','DESC');
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result->getId():false;
    }

    public function nextArticle($id,$categoryId)
    {
        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('a')->from('AppBundle:Article','a')
            ->where("a.id>{$id}")
            ->andWhere("a.categoryId={$categoryId}")
            ->setMaxResults(1)->orderBy('a.id','DESC');
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result->getId():false;
    }
    public function prevCase($id,$categoryId)
    {
        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('a')->from('AppBundle:Cases','a')
            ->where("a.id<{$id}")
            ->andWhere("a.categoryId={$categoryId}")
            ->setMaxResults(1)->orderBy('a.id','DESC');
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result->getId():false;
    }

    public function nextCase($id,$categoryId)
    {
        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('a')->from('AppBundle:Cases','a')
            ->where("a.id>{$id}")
            ->andWhere("a.categoryId={$categoryId}")
            ->setMaxResults(1)->orderBy('a.id','DESC');
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result->getId():false;
    }

}