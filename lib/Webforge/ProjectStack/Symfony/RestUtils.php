<?php

namespace Webforge\ProjectStack\Symfony;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;

class RestUtils {

  protected $fqn;
  protected $singular;
  protected $plural;
  protected $em;
  protected $exceptionsHandler;

  public function __construct($fqn, $plural, $singular, $em) {
    $this->singular = $singular;
    $this->plural = $plural;
    $this->fqn = $fqn;
    $this->em = $em;
  }

  public function hydrateEntity($id) {
    $criteria = array('id'=>$id);
    $entity = $this->getRepository()->findOneBy($criteria);

    if ($entity instanceof $this->fqn) {
      return $entity;
    } else {
      throw new NotFoundHttpException('Resource Not found: '.$this->fqn.':'.$id);
    }
  }

  public function saveEntity($entity) {
    $this->persist($entity);

    $this->flush();

    return $this->noContentResponse();
  }

  public function deleteEntity($id) {
    $entity = $this->hydrateEntity($id);

    $this->em->remove($entity);

    $this->flush();

    return $entity;
  }

  public function getEntitiesView($plural = NULL) {
    $view = $this->view(array(
      $this->plural => $this->getRepository()->findAll()
    ));

    $view->setFormat('json');

    return $view;
  }

  public function getEntityView($id) {
    $view = $this->view(array(
      $this->singular => $this->hydrateEntity($id)
    ));

    $view->setFormat('json');

    return $view;
  }

  public function persist($entity) {
    $this->em->persist($entity);
  }

  public function flush() {
    try {
      $this->em->flush();

    } catch (\Exception $e) {
      throw $e;
      //throw $this->exceptionsHandler->convert($e);
    }
  }

  public function convertUniqueConstraintExceptionTo($exception) {
    $this->exceptionsHandler->registerConverter('Webforge\Doctrine\Exceptions\UniqueConstraintException', function($e) use ($exception) {
      return $exception;
    });
  }

  public function getRepository() {
    return $this->em->getRepository($this->fqn);
  }

  public function noContentResponse() {
    return new Response(NULL, Codes::HTTP_NO_CONTENT);
  }  

  public function view($data = NULL, $statusCode = NULL, array $headers = array()) {
    return View::create($data, $statusCode, $headers);
  }
}
