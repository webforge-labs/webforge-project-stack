<?php

namespace Webforge\ProjectStack\Views;

use Webforge\Common\ClassUtil;
use Doctrine\ORM\EntityManager;
use Webforge\Common\Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webforge\Framework\Project;
use Webforge\Common\String as S;

class Factory {

  protected $em;

  protected $router;

  protected $project;

  public function __construct(Project $project, EntityManager $em, UrlGeneratorInterface $router) {
    $this->em = $em;
    $this->project = $project;
    $this->router = $router;
  }

  public function getView($className, $vars = array()) {
    //$fqn = ClassUtil::expandNamespace($className, $this->project->getNamespace().'\\Views');
    $fqn = S::expand($className, $this->project->getNamespace().'\\Views\\', S::START);

    $view = $this->createView($fqn, $vars);
    
    $this->injectView($view);

    $view->init();

    return $view;
  }

  protected function injectView(View $view) {
  }

  protected function createView($fqn, $vars = array()) {
    if(!class_exists($fqn)) {
      throw new Exception('The view with FQN: '.$fqn.' Cannot be created: Class Not Found!');
    }

    $view = new $fqn($vars);

    return $view;
  }
}
