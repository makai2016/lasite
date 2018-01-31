<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-26
 * Time: 下午3:09
 */

namespace AppBundle\Controller\Backend;


use AppBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function indexAction()
    {
        return $this->render('@Backend\home\index.html.twig');
    }
}