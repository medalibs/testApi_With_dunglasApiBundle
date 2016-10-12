<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;


use Dunglas\ApiBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Dunglas\ApiBundle\Api\Resource;
use Dunglas\ApiBundle\Event\Events;
use Dunglas\ApiBundle\Event\DataEvent;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrineOrmPaginator;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
/**
 * Description of UserController
 *
 * @author mas
 */
class UserController  extends ResourceController{
    
    public function verifAction(Request $request)
    {
        
        $jsonObject = json_decode($request->getContent());
        
       // echo $request->getContent();exit;
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->findOneBy(array('id' => $jsonObject->id));
        if (!$user) {
            throw $this->createNotFoundException("User Not Found");
        }
        
        $resource = $this->getResource($request);
        $object = $this->findOrThrowNotFound($resource, $jsonObject->id);

        if($object->getEnabled()){
            throw $this->createNotFoundException("User disabled");
        }
        $this->get('event_dispatcher')->dispatch(Events::RETRIEVE, new DataEvent($resource, $object));

        return $this->getSuccessResponse($resource, $object);
        
    }
}
