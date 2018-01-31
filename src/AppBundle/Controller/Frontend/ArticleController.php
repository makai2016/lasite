<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-29
 * Time: 上午1:49
 */

namespace AppBundle\Controller\Frontend;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\CategoryRepository;

class ArticleController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $articles = self::_article($request->query->getInt('page',1));
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::ARTICLE_TYPE]);
        $view = $this->view(compact('articles','categorys'));
        $view->setTemplate('@Frontend\article\index.html.twig');
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction(Request $request,$category)
    {
        $current = $this->getManager()->getRepository('AppBundle:Category')->findOneBy(['route'=>$category,'type'=>CategoryRepository::ARTICLE_TYPE]);
        if(!$current){
            throw $this->createNotFoundException();
        }
        $articles = self::_article($request->query->getInt('page',1),$current);
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::ARTICLE_TYPE]);
        $view = $this->view(compact('articles','categorys','current'));
        $view->setTemplate('@Frontend\article\index.html.twig');
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function infoAction(Request $request,$id)
    {
        $article = $this->getManager()
            ->getRepository('AppBundle:Article')->findWithCategory($id);
        if(!$article){
            throw $this->createNotFoundException();
        }
        $recommends = self::recommends();
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::ARTICLE_TYPE]);
        $article->setTags($this->get('app.tag_peer')->getArticleTag($id));
        $view = $this->view(compact('article','categorys','recommends'))
            ->setTemplate('@Frontend\article\info.html.twig');

        return $this->handleView($view);
    }

    /**
     * @param $page
     * @param null $category
     * @param int $limit
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    private function _article($page,$category=null, $limit=10)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('a')
            ->from('AppBundle:Article','a')
            ->where('a.deleted=0')->orderBy('a.rank','DESC')
            ->orderBy('a.rank','DESC')
            ->orderBy('a.id','DESC');
        if($category instanceof Category){
            $qb->andWhere("a.categoryId={$category->getId()}");
        }
        $articles = $this->pagination($qb,$page,$limit);
        $items = $articles->getItems();
        $tagPeer = $this->get('app.tag_peer');
        array_walk($items,function (&$item,$key)use ($tagPeer){
            $c = $this->getManager()->getRepository('AppBundle:Category')->find($item->getCategoryId());
            $f = $tagPeer->getArticleTag($item->getId());
            $item->setCategory($c)->setTags($f);
        });
        $articles->setItems($items);
        return $articles;
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