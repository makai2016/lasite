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

class CaseCategoryController extends AbstractController
{
    public function listAction(Request $request)
    {
        $categorys = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['type'=>CategoryRepository::CASE_TYPE]);
        $view = $this->view('')
            ->setData(compact('categorys'))
            ->setTemplate('@Backend\case_category\list.html.twig');
        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        return $this->render('@Backend\case_category\create.html.twig');
    }
    public function editAction(Request $request,$id)
    {
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneBy(['id'=>$id,'type'=>CategoryRepository::CASE_TYPE]);
        if(!$category){
            return $this->error(['message'=>'分类Id不存在'],404);
        }
        return $this->render('@Backend\case_category\edit.html.twig',compact('category'));
    }
}