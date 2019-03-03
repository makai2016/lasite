<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-28
 * Time: 下午4:19
 */

namespace AppBundle\Controller\Bapi;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\SecondCategory;
use AppBundle\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

class SecondCategoryController extends AbstractController
{
    public function postSecondcategoryAction(Request $request)
    {
        try{
            $category = new SecondCategory();
            $this->mapping($category,$request->request->all());
            $this->validate($category);

            $this->save($category);
            return;
        }catch (ValidationException $e){
            return $this->handleView($this->errorValidate($e));
        }
    }

    public function putSecondcategoryAction(Request $request,$id)
    {
        $category = $this->getDoctrine()->getRepository('AppBundle:SecondCategory')->find($id);

        if(!$category){
            return $this->error(['message'=>'分类Id不存在'],404);
        }
        try{
            $this->mapping($category,$request->request->all());
            $this->validate($category);

            $this->save($category);
            return;
        }catch (ValidationException $e){
            return $this->handleView($this->errorValidate($e));
        }
    }

    public function deleteSecondcategoryAction($id)
    {
        $category = $this->getDoctrine()->getRepository('AppBundle:SecondCategory')->find($id);
        if(!$category){
            return $this->error(['message'=>'分类Id不存在'],404);
        }
        $this->delete($category);
        return;
    }
}