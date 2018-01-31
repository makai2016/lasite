<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-30
 * Time: 下午11:48
 */

namespace AppBundle\Controller\Bapi;


use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CacheController extends AbstractController
{
    public function getClearcacheAction()
    {
        /*$cmd ='php '.$this->getParameter('kernel.root_dir').'/console cache:clear --env=prod';
        $res = `$cmd`;*/

        $realCacheDir = $this->getParameter('kernel.cache_dir');
        $filesystem = $this->get('filesystem');
        $filesystem->remove($realCacheDir);
        $filesystem->mkdir($realCacheDir);
        $filesystem->mkdir($realCacheDir.'/jms_serializer');
        //$response = new Response(str_replace(PHP_EOL, '<BR>', $res),200);
        $response = new Response('[OK] 清除成功');
        return $response;
    }
}