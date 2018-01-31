<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-26
 * Time: 下午5:35
 */

namespace AppBundle\Controller\Backend;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    public function listAction(Request $request)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('a')
            ->from('AppBundle:Article','a')
            ->where('a.deleted=0');
        $articles = $this->pagination($qb,$request->query->getInt('page',1),10);
        $items = $articles->getItems();
        array_walk($items,function (&$item,$key){
            $c=$this->getManager()->getRepository('AppBundle:Category')->find($item->getCategoryId());
            $item->setCategory($c);
        });
        $articles->setItems($items);
        $view = $this->view(compact('articles'))
            ->setTemplate('@Backend\article\list.html.twig');
        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::ARTICLE_TYPE]);
        $view = $this->view()
            ->setData(compact('categorys'))
            ->setTemplate('@Backend\article\create.html.twig');
        return $this->handleView($view);
    }

    /**
     * Edit Article
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request,$id)
    {
        $article = $this->getManager()->getRepository('AppBundle:Article')->find($id);
        if(!$article){throw $this->createNotFoundException();}


        $tags = $this->get('app.tag_peer')->getArticleTag($id);
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::ARTICLE_TYPE]);

        $view = $this->view()
            ->setData(compact('article','categorys','tags'))
            ->setTemplate('@Backend\article\edit.html.twig');

        return $this->handleView($view);
    }
}