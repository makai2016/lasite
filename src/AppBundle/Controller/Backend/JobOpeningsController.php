<?php
/**
 * Created by PhpStorm.
 * User: kkma
 * Date: 18-5-10
 * Time: 下午9:04
 */

namespace AppBundle\Controller\Backend;


use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class JobOpeningsController extends AbstractController
{
    private  $view;
    public function __construct()
    {
        $this->view = parent::view();
    }

    public function listAction(Request $request)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('j')
            ->from('AppBundle:JobOpenings','j')
            ->orderBy('j.id','DESC');
        $jobs = $this->pagination($qb,$request->query->getInt('page',1),30);
        $this->view->setData(compact('jobs'))->setTemplate('@Backend\job\list.html.twig');
        return parent::handleView($this->view);
    }

    public function createAction(Request $request)
    {
        $this->view->setTemplate('@Backend\job\create.html.twig');
        return parent::handleView($this->view);
    }

    public function editAction(Request $request, $id)
    {
        $job = $this->getManager()->getRepository('AppBundle:JobOpenings')->find($id);
        if(!$job){throw $this->createNotFoundException();}

        $this->view->setData(compact('job'))->setTemplate('@Backend\job\edit.html.twig');
        return parent::handleView($this->view);
    }
}