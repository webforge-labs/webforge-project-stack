<?php

namespace Webforge\ProjectStack;

use Webforge\Framework\Project;
use Webforge\Doctrine\Container as DoctrineContainer;
use InvalidArgumentException;

class Container {

  /**
   * @var Webforge\Framework\Project
   */
  protected $project;

  /**
   * @var Webforge\Doctrine\Container
   */
  protected $doctrineContainer;
  
  public function __construct(Project $project) {
    $this->project = $project;
    $this->initProject($this->project);
  }

  protected function initProject(Project $project) {
    
  }

  /**
   * @return Webforge\Doctrine\Container
   */
  public function getDoctrineContainer() {
    if (!isset($this->doctrineContainer)) {
      $this->doctrineContainer = new DoctrineContainer();
      $this->initDoctrineContainer($this->doctrineContainer);
    }

    return $this->doctrineContainer;
  }

  protected function initDoctrineContainer(DoctrineContainer $dcc) {
    try {
      $this->project->dir('doctrine-entities');
    } catch (InvalidArgumentException $e) {
      $this->project->defineDirectory(
        'doctrine-entities', 
        $this->project->getNamespaceDirectory()
          ->sub('Entities')
          ->makeRelativeTo($this->project->getRootDirectory())
      ); 
    }

    // proxies should be defined anyway (as default)
    $dcc->setProxyDirectory($this->project->dir('doctrine-proxies'));

    $dcc->initDoctrine(
      $this->project->getConfiguration()->req('db'),
      array($this->project->dir('doctrine-entities'))
    );
  }
  
  /**
   * @param Webforge\Doctrine\Container $doctrineContainer
   * @chainable
   */
  public function injectDoctrineContainer(DoctrineContainer $doctrineContainer) {
    $this->doctrineContainer = $doctrineContainer;
    return $this;
  }
  
  /**
   * @return Webforge\Framework\Project
   */
  public function getProject() {
    return $this->project;
  }
}
