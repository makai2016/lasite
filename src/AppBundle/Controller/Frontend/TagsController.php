<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-29
 * Time: 上午8:32
 */

namespace AppBundle\Controller\Frontend;


use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TagsController extends AbstractController
{
    public function indexAction(Request $request,$tagId)
    {
        $peer = $this->get('app.tag_peer');
        $tag = $peer->getTag($tagId);
        $targets = $this->pagination($peer->getTarget($tagId),$request->query->getInt('page',1),20);
        $view = $this->view(compact('tag','targets'))
            ->setTemplate('@Frontend\tags\index.html.twig');
        return $this->handleView($view);
    }
}