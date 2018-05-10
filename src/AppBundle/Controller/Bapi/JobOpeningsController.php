<?php

namespace AppBundle\Controller\Bapi;

use AppBundle\Entity\JobOpenings;
use AppBundle\Exception\ValidationException;
use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class JobOpeningsController extends AbstractController
{
    public function postJobAction(Request $request)
    {
        try{

            $job = new JobOpenings();
            $this->mapping($job,$request->request->all());
            $this->validate($job);

            $this->getManager()->beginTransaction();
            $this->save($job);

            $this->getManager()->commit();

            return ;
        }catch (ValidationException $e) {
            return $this->handleView($this->errorValidate($e));
        }catch (\Exception $e){
            $this->getManager()->rollback();
            return $this->error(['message'=>$e->getMessage()],500);
        }
    }

    public function putJobAction(Request $request,$id)
    {
        $job = $this->getManager()->getRepository('AppBundle:JobOpenings')->find($id);
        if(!$job){throw $this->createNotFoundException();}
        try{
            $this->mapping($job,$request->request->all());
            $this->validate($job);

            $this->getManager()->beginTransaction();
            $this->save($job);

            $this->getManager()->commit();

            return ;
        }catch (ValidationException $e) {
            return $this->handleView($this->errorValidate($e));
        }catch (\Exception $e){
            $this->getManager()->rollback();
            return $this->error(['message'=>$e->getMessage()],500);
        }
    }

    public function deleteJobAction($id)
    {
        $job = $this->getManager()->getRepository('AppBundle:JobOpenings')->find($id);
        if(!$job){throw $this->createNotFoundException();}
        $this->delete($job);
        return;
    }
}