<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-31
 * Time: 上午1:08
 */

namespace AppBundle\Controller\Backend;


use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    public function listAction(Request $request)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('u')->from('AppBundle:User','u');

        $users = $this->pagination($qb,$request->query->getInt('page',1),10);

        $view = $this->view(compact('users'))
            ->setTemplate('@Backend/user/list.html.twig');
        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        $view = $this->view()->setTemplate('@Backend/user/create.html.twig');
        return $this->handleView($view);
    }

    public function editAction(Request $request,$id)
    {
        $user = $this->getManager()->getRepository('AppBundle:User')->find($id);
        if(!$user){
            throw $this->createNotFoundException();
        }
        $view = $this->view(compact('user'))
            ->setTemplate('@Backend/user/edit.html.twig');
        return $this->handleView($view);
    }
}