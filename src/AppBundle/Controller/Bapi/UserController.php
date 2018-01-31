<?php
/**
 * Created by PhpStorm.
 * User: aoc
 * Date: 18-1-31
 * Time: 上午1:06
 */

namespace AppBundle\Controller\Bapi;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    public function postUserAction(Request $request)
    {
        try{
            $user = new User();
            $this->mapping($user,$request->request->all(),['filter'=>['plassword']]);

            $encode = $this->get('security.password_encoder');
            $user->setPassword($encode->encodePassword($user,$user->getPlanPassword()));

            $this->save($user);
            return;
        }catch (ValidationException $e){
            return $this->errorValidate($e);
        }
    }

    public function putUserAction(Request $request, $id)
    {
        $user = $this->getManager()->getRepository('AppBundle:User')->find($id);
        if(!$user){
            throw $this->createNotFoundException();
        }
        try{
            $this->mapping($user,$request->request->all(),['only'=>['planPassword','isActive','role']]);

            if($user->getPlanPassword()){
                $encode = $this->get('security.password_encoder');
                $user->setPassword($encode->encodePassword($user,$user->getPlanPassword()));
            }
            $this->save($user);
            return;
        }catch (ValidationException $e){
            return $this->errorValidate($e);
        }

    }

    public function deleteUserAction($id)
    {
        if($id==$this->getUser()->getId()){ throw $this->createAccessDeniedException('不能删除自己');}
        $user = $this->getManager()->getRepository('AppBundle:User')->find($id);
        if(!$user){
            throw $this->createNotFoundException();
        }

        $this->getManager()->remove($user);
        $this->getManager()->flush();
        return;
    }
}