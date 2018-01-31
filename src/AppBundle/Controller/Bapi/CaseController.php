<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-29
 * Time: 上午12:31
 */

namespace AppBundle\Controller\Bapi;

use AppBundle\Entity\Cases;
use AppBundle\Exception\AppException;
use AppBundle\Services\TagPeer;
use AppBundle\Exception\TagPeerException;
use AppBundle\Exception\ValidationException;
use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class CaseController extends AbstractController
{
    public function postCaseAction(Request $request)
    {
        try{
            $tagPeer = $this->get('app.tag_peer');
            $tags = explode(',',$request->request->get('tags'));
            TagPeer::checkTags($tags);

            $case = new Cases();
            $data = array_merge($request->request->all(),$request->files->all());
            $this->mapping($case,$data);
            $this->validate($case);
            if($request->files->has('banner_image')){
                $case->setBanner(self::_savebanner($request->files->get('banner_image')));
            }
            $this->getManager()->beginTransaction();
            $this->save($case);
            $tagPeer->setCaseTag($case->getId(),$tags);
            $this->getManager()->commit();

            return ;
        }catch (ValidationException $e) {
            return $this->handleView($this->errorValidate($e));
        }catch (TagPeerException $e){
            return $this->error(['message'=>$e->getMessage()]);
        }catch (AppException $e){
            return $this->error(['message'=>$e->getMessage()]);
        }catch (\Exception $e){
            $this->getManager()->rollback();
            return $this->error(['message'=>$e->getMessage()],500);
        }
    }
    public function putCaseAction(Request $request,$id)
    {
        $case = $this->getManager()->getRepository('AppBundle:Cases')->find($id);
        if(!$case){throw $this->createNotFoundException();}
        try{
            $tagPeer = $this->get('app.tag_peer');
            $tags = explode(',',$request->request->get('tags'));
            TagPeer::checkTags($tags);

            $data = array_merge($request->request->all(),$request->files->all());
            $this->mapping($case,$data);
            $this->validate($case);
            if($request->files->has('banner_image')){
                $case->setBanner(self::_savebanner($request->files->get('banner_image')));
            }
            $this->getManager()->beginTransaction();
            $this->save($case);
            $tagPeer->setCaseTag($case->getId(),$tags);
            $this->getManager()->commit();

            return ;
        }catch (ValidationException $e) {
            return $this->handleView($this->errorValidate($e));
        }catch (TagPeerException $e){
            return $this->error(['message'=>$e->getMessage()]);
        }catch (AppException $e){
            return $this->error(['message'=>$e->getMessage()]);
        }catch (\Exception $e){
            $this->getManager()->rollback();
            return $this->error(['message'=>$e->getMessage()],500);
        }
    }

    public function deleteCaseAction($id)
    {
        $article = $this->getManager()->getRepository('AppBundle:Cases')->find($id);
        if(!$article){throw $this->createNotFoundException();}
        $this->delete($article);
        return;
    }

    private function _savebanner(UploadedFile $file)
    {
        $fileType = $file->getMimeType();
        if(strpos($fileType,'image')===false){
            throw new AppException('封面不是一个图片文件');
        }
        $fliename =uniqid().'.'.$file->guessExtension();
        $path =__DIR__.'/../../../../uploads/cases/';
        $file->move($path,$fliename);
        return '/uploads/cases/'.$fliename;
    }
}