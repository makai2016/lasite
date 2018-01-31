<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $news = $this->getManager()->getRepository('AppBundle:Article')->recommendArticle();
        $cases = self::_hotcase();
        $view = $this->view(compact('news','cases'))->setTemplate('@Frontend\home\index.html.twig');
        return $this->handleView($view);
    }


    private function _hotcase($limit=3)
    {
        $items = $items = $this->getManager()->getRepository('AppBundle:Cases')->homeCases($limit);
        $tagPeer = $this->get('app.tag_peer');
        array_walk($items,function (&$item,$key)use ($tagPeer){
            $f = $tagPeer->getCaseTag($item->getId());
            $item->setTags($f);
        });
        return $items;
    }
}
