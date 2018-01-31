<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-26
 * Time: 下午2:58
 */

namespace AppBundle\Controller\Security;


use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Security\login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    public function ckuploadAction(Request $request)
    {
        $response = new Response();
        if(!$request->files->has('upload')){
            return $response->setContent('no file upload');
        }
        $file  =$request->files->get('upload');
        if(!($file instanceof UploadedFile)){
            return $response->setContent('no file upload');
        }
        $fileType = $file->getMimeType();
        if(strpos($fileType,'image')===false){
           return $response->setContent('一个图片文件');
        }
        $prepath ='/uploads/'.date('Ymd').'/';
        $fliename =uniqid().'.'.$file->guessExtension();
        $path =__DIR__.'/../../../../'.$prepath;
        $file->move($path,$fliename);
        $result = $this->getParameter('img_prefix').$prepath.$fliename;

        $callback = $request->query->get("CKEditorFuncNum");
        $response->setContent("<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction($callback,'$result')</script>");

        return $response;
    }
}