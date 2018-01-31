<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-28
 * Time: 下午3:02
 */

namespace AppBundle\Controller\Bapi;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Article;
use AppBundle\Exception\TagPeerException;
use AppBundle\Exception\ValidationException;
use AppBundle\Services\TagPeer;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{

    public function postArticleAction(Request $request)
    {
        try{
            $tagPeer = $this->get('app.tag_peer');
            $tags = explode(',',$request->request->get('tags'));
            TagPeer::checkTags($tags);

            $article = new Article();
            $data = array_merge($request->request->all(),$request->files->all());
            $this->mapping($article,$data);
            $this->validate($article);

            $this->getManager()->beginTransaction();
            $this->save($article);
            $tagPeer->setArticleTag($article->getId(),$tags);
            $this->getManager()->commit();

            return ;
        }catch (ValidationException $e) {
            return $this->handleView($this->errorValidate($e));
        }catch (TagPeerException $e){
            return $this->error(['message'=>$e->getMessage()]);
        }catch (\Exception $e){
            $this->getManager()->rollback();
            return $this->error(['message'=>$e->getMessage()],500);
        }
    }

    public function putArticleAction(Request $request,$id)
    {
        $article = $this->getManager()->getRepository('AppBundle:Article')->find($id);
        if(!$article){throw $this->createNotFoundException();}
        try{
            $tagPeer = $this->get('app.tag_peer');
            $tags = explode(',',$request->request->get('tags'));
            TagPeer::checkTags($tags);

            $data = array_merge($request->request->all(),$request->files->all());
            $this->mapping($article,$data);
            $this->validate($article);

            $this->getManager()->beginTransaction();
            $this->save($article);
            $tagPeer->setArticleTag($article->getId(),$tags);
            $this->getManager()->commit();

            return ;
        }catch (ValidationException $e) {
            return $this->handleView($this->errorValidate($e));
        }catch (TagPeerException $e){
            return $this->error(['message'=>$e->getMessage()]);
        }catch (\Exception $e){
            $this->getManager()->rollback();
            return $this->error(['message'=>$e->getMessage()],500);
        }
    }

    public function deleteArticleAction($id)
    {
        $article = $this->getManager()->getRepository('AppBundle:Article')->find($id);
        if(!$article){throw $this->createNotFoundException();}
        $this->delete($article);
        return;
    }
}