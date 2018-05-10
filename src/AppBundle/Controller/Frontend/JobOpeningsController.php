<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-29
 * Time: 上午1:49
 */

namespace AppBundle\Controller\Frontend;

use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class JobOpeningsController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('j')
            ->from('AppBundle:JobOpenings','j')
            ->orderBy('j.id','DESC');
        $jobs = $this->pagination($qb,$request->query->getInt('page',1),10);

        $view = $this->view(compact('jobs'));
        $view->setTemplate('@Frontend\jobs\index.html.twig');
        return $this->handleView($view);
    }

   
    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function infoAction(Request $request,$id)
    {
        $job = $this->getManager()
            ->getRepository('AppBundle:JobOpenings')->find($id);
        if(!$job){
            throw $this->createNotFoundException();
        }
        $recommends = self::recommends();
        $view = $this->view(compact('job','recommends'))
            ->setTemplate('@Frontend\jobs\info.html.twig');

        return $this->handleView($view);
    }

    /**
     * @param $id
     *
     * @return array
     */
    private function recommends($limit=4)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('a')
            ->from('AppBundle:Article','a')
            ->where('a.deleted=0')
            ->andWhere('a.recommend=1')
            ->orderBy('a.rank','DESC')
            ->orderBy('a.id','DESC')
            ->setMaxResults($limit);
        $items = $qb->getQuery()->getResult();
        $tagPeer = $this->get('app.tag_peer');
        array_walk($items,function (&$item,$key)use ($tagPeer){
            $c = $this->getManager()->getRepository('AppBundle:Category')->find($item->getCategoryId());
            $f = $tagPeer->getArticleTag($item->getId());
            $item->setCategory($c)->setTags($f);
        });

        return $items;
    }

}