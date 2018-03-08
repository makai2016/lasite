<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-29
 * Time: 上午4:14
 */

namespace AppBundle\Controller\Frontend;


use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\CategoryRepository;
use AppBundle\Entity\Category;

class CaseController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $cases = self::_case($request->query->getInt('page',1));
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::CASE_TYPE]);
        $view = $this->view(compact('cases','categorys'));
        $view->setTemplate('@Frontend\case\index.html.twig');
        return $this->handleView($view);
    }

    public function categoryAction(Request $request,$category)
    {
        $current = $this->getManager()->getRepository('AppBundle:Category')
            ->findOneBy(['route'=>$category,'type'=>CategoryRepository::CASE_TYPE]);
        if(!$current){
            throw $this->createNotFoundException();
        }
        $cases = self::_case($request->query->getInt('page',1),$current);
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::CASE_TYPE]);
        $view = $this->view(compact('cases','categorys','current'));
        $view->setTemplate('@Frontend\case\index.html.twig');
        return $this->handleView($view);
    }

    public function infoAction(Request $request,$id)
    {
        $case = $this->getManager()
            ->getRepository('AppBundle:Cases')->findWithCategory($id);
        if(!$case){
            throw $this->createNotFoundException();
        }
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::CASE_TYPE]);
        $case->setTags($this->get('app.tag_peer')->getCaseTag($id));
        $view = $this->view(compact('case','categorys'))
            ->setTemplate('@Frontend\case\info.html.twig');

        return $this->handleView($view);
    }

    private function _case($page,$category=null, $limit=12)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:Cases','c')
            ->where('c.deleted=0')->orderBy('c.rank')
            ->orderBy('c.id','DESC');
        if($category instanceof Category){
            $qb->andWhere("c.categoryId={$category->getId()}");
        }
        $articles = $this->pagination($qb,$page,$limit);
        $items = $articles->getItems();
        $tagPeer = $this->get('app.tag_peer');
        array_walk($items,function (&$item,$key)use ($tagPeer){
            $c = $this->getManager()->getRepository('AppBundle:Category')->find($item->getCategoryId());
            $f = $tagPeer->getCaseTag($item->getId());
            $item->setCategory($c)->setTags($f);
        });
        $articles->setItems($items);
        return $articles;
    }

}