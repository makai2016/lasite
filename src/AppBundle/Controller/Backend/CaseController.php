<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-26
 * Time: 下午5:35
 */

namespace AppBundle\Controller\Backend;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Article;
use AppBundle\Entity\CategoryRepository;
use Doctrine\ORM\Query\Expr\Join;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class CaseController extends AbstractController
{
    /**
     * Get cases
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:Cases','c')
            ->where('c.deleted=0');
        $cases = $this->pagination($qb,$request->query->getInt('page',1),10);
        $items = $cases->getItems();
        array_walk($items,function (&$item,$key){
            $c=$this->getManager()->getRepository('AppBundle:Category')->find($item->getCategoryId());
            $item->setCategory($c);
        });
        $cases->setItems($items);
        $view = $this->view(compact('cases'))
            ->setTemplate('@Backend\case\list.html.twig');
        return $this->handleView($view);
    }

    /**
     * Create Cases
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::CASE_TYPE]);
        $view = $this->view()
            ->setData(compact('categorys'))
            ->setTemplate('@Backend\case\create.html.twig');
        return $this->handleView($view);
    }

    /**
     * Edit Cases
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request,$id)
    {
        $case = $this->getManager()->getRepository('AppBundle:Cases')->find($id);
        if(!$case){throw $this->createNotFoundException();}


        $tags = $this->get('app.tag_peer')->getCaseTag($id);
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::CASE_TYPE]);

        $view = $this->view()
            ->setData(compact('case','categorys','tags'))
            ->setTemplate('@Backend\case\edit.html.twig');

        return $this->handleView($view);
    }
}