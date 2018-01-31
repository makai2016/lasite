<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-28
 * Time: 下午4:19
 */

namespace AppBundle\Controller\Bapi;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Category;
use AppBundle\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    public function postCategoryAction(Request $request)
    {
        try{
            $category = new Category();
            $this->mapping($category,$request->request->all());
            $this->validate($category);
            $this->save($category);
            return;
        }catch (ValidationException $e){
            return $this->handleView($this->errorValidate($e));
        }
    }

    public function putCategoryAction(Request $request,$id)
    {
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
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

    public function deleteCategoryAction($id)
    {
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
        if(!$category){
            return $this->error(['message'=>'分类Id不存在'],404);
        }
        $this->delete($category);
        return;
    }
}