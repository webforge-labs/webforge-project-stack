<?php

namespace Webforge\ProjectStack\Views;

use Webforge\Common\ClassUtil;
use Doctrine\ORM\EntityManager;
use Webforge\Common\Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webforge\Framework\Project;

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
    $fqn = ClassUtil::expandNamespace($className, $this->project->getNamespace().'\\Views');

    $view = $this->createView($fqn, $vars);
    
    $this->injectView($view);

    return $view;
  }

  protected function injectView(View $view) {
    if ($view instanceof UrlGenerating) {
      $view->setRouter($this->router);
    }

    if ($view instanceof MarkdownTransforming) {
      $view->setMarkdownParser($this->markdownParser);
    }
  }

  protected function createView($fqn, $vars = array()) {
    if(!class_exists($fqn)) {
      throw new Exception('The view with FQN: '.$fqn.' Cannot be created: Class Not Found!');
    }

    $view = new $fqn($vars);

    return $view;
  }
}
