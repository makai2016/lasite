<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-28
 * Time: 下午11:49
 */

namespace AppBundle\Controller\Backend;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;

class SecondCategoryController extends AbstractController
{
    public function listAction(Request $request)
    {
        $fcategorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        $categorys = $this->getDoctrine()->getRepository('AppBundle:SecondCategory')->findAll();


        $view = $this->view('')
            ->setData(compact('categorys','fcategorys'))
            ->setTemplate('@Backend\second_category\list.html.twig');
        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        $categorys=$this->getManager()
            ->getRepository('AppBundle:Category')
            ->createQueryBuilder('c')
            ->select(['c.id','c.name','c.route'])
            ->where('c.type='.CategoryRepository::CASE_TYPE)
            ->getQuery()->getArrayResult();

        return $this->render('@Backend\second_category\create.html.twig',compact('categorys'));
    }
    public function editAction(Request $request,$id)
    {
        $categorys=$this->getManager()
            ->getRepository('AppBundle:Category')
            ->createQueryBuilder('c')
            ->select(['c.id','c.name','c.route'])
            ->where('c.type='.CategoryRepository::CASE_TYPE)
            ->getQuery()->getArrayResult();
        $entity = $this->getDoctrine()->getRepository('AppBundle:SecondCategory')->find($id);
        if(!$entity){
            return $this->error(['message'=>'分类Id不存在'],404);
        }
        return $this->render('@Backend\second_category\edit.html.twig',compact('entity','categorys'));
    }
}