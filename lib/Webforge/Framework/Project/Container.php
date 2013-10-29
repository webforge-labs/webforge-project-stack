<?php

namespace Webforge\Framework\Project;

use Webforge\Framework\Project;
use Webforge\Doctrine\Container as DoctrineContainer;

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
  
  /**
   * @param Webforge\Framework\Project $project
   * @chainable
   */
  public function injectProject(Project $project) {
    $this->project = $project;
    return $this;
  }
}
