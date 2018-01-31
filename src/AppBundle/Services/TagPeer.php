<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-28
 * Time: 下午9:20
 */

namespace AppBundle\Services;

use AppBundle\Entity\Tag;
use AppBundle\Entity\TagMapRepository;
use AppBundle\Exception\TagPeerException;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TagPeer
{

    const ARTICLE_TYPE = 1;
    const CASE_TYPE = 2;
    const TAG_NAME_LENGTH = 250;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @param $tagId
     *
     * @return array
     */
    public function getTarget($tagId)
    {
        $target = [];
        $em = $this->container->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();
        $qb->select('m')
            ->from('AppBundle:TagMap','m')
            ->where("m.tagId={$tagId}");
        $tagmaps = $qb->getQuery()->getResult();
        foreach ($tagmaps as $tagmap){
            switch ($tagmap->getType()){
                case self::ARTICLE_TYPE:
                    $entity = $em->getRepository('AppBundle:Article')->findWithCategory($tagmap->getTargetId());
                    $entity->setTags($this->getArticleTag($entity->getId()));
                    $target[]=$entity;
                    break;
                case self::CASE_TYPE:
                    $entity = $em->getRepository('AppBundle:Cases')->findWithCategory($tagmap->getTargetId());
                    $entity->setTags($this->getCaseTag($entity->getId()));
                    $target[]=$entity;
                    break;
            }
        }
        return $target;
    }

    /**
     * @param $id
     * @return Tag|object
     */
    public function getTag($id)
    {
        $tag =$this->container->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Tag')
            ->find($id);

        return $tag;
    }

    /**
     *
     * @param $articleId
     *
     * @return array
     */
    public function getArticleTag($articleId)
    {
        $qb = self::tagQuery(self::ARTICLE_TYPE);
        $qb->andWhere("m.targetId={$articleId}");
        return $qb->getQuery()->getResult();
    }

    /**
     *
     * @param $articleId
     *
     * @return array
     */
    public function getCaseTag($articleId)
    {
        $qb = self::tagQuery(self::CASE_TYPE);
        $qb->andWhere("m.targetId={$articleId}");
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $articleId
     * @param array $tags
     *
     * @return array
     */
    public function setArticleTag($articleId,array $tags)
    {
        return self::saveTags($tags,$articleId,self::ARTICLE_TYPE);
    }

    /**
     * @param $caseId
     * @param array $tags
     *
     * @return array
     */
    public function setCaseTag($caseId,array $tags)
    {
        return self::saveTags($tags,$caseId,self::CASE_TYPE);
    }

    /**
     * @param array $tags
     */
    public static function checkTags(array &$tags)
    {
        array_walk($tags,function (&$item,$key){
            if(!is_string($item)){
                throw new TagPeerException('标签名称不是一个字符');
            }elseif (self::TAG_NAME_LENGTH<mb_strlen($item)){
                throw new TagPeerException('标签名称长度不能大250个字符');

            }
            $item =trim($item);
        });
    }

    /**
     * @param $type
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function tagQuery($type)
    {
        $qb = $this->container->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('t')
            ->from('AppBundle:Tag','t')
            ->leftJoin('AppBundle:TagMap','m',Join::WITH,'t.id=m.tagId')
            ->where("m.type={$type}");
        return $qb;
    }

    /**
     * @param array $tags
     *
     * @return array
     */
    private function saveTags(array $tags,$targetId,$type)
    {
        $tags = array_filter($tags);
        if(!count($tags)){return;}
        $sql = 'INSERT INTO tag SET `name`=\'%1$s\' ON DUPLICATE KEY UPDATE `name`=\'%1$s\'';
        foreach ($tags as $tag){
            $this->container->get('doctrine.orm.entity_manager')->getConnection()->exec(sprintf($sql,$tag));
        }

        $qb = $this->container->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('t')->from('AppBundle:Tag','t')->where($qb->expr()->in('t.name',$tags));
        $tagEntitys = $qb->getQuery()->getResult();
        $tagIds = [];
        array_walk($tagEntitys,function ($item,$key)use (&$tagIds){
            $tagIds[]=$item->getId();
        });
        self::saveTagMaps($targetId,$tagIds,$type);
        return $tagEntitys;
    }

    /**
     * @param $targetId
     * @param array $tagIds
     * @param $type
     */
    private function saveTagMaps($targetId,array $tagIds,$type)
    {
        $sql = 'INSERT INTO tag_map SET tag_id=\'%1$d\',target_id=\'%2$d\',`type`=\'%3$d\' ON DUPLICATE KEY UPDATE `type`=\'%3$d\'';
        foreach ($tagIds as $tagId)
        {
            $this->container->get('doctrine.orm.entity_manager')
                ->getConnection()
                ->exec(sprintf(
                    $sql,
                    $tagId,
                    $targetId,
                    $type
                ));
        }
    }
}