<?php

namespace Webforge\ProjectStack;

use Webforge\Framework\Project;
use Webforge\Doctrine\Container as DoctrineContainer;
use InvalidArgumentException;
use Webforge\ProjectStack\Symfony\Kernel;
use Webforge\Setup\BootContainer as WebforgeBootContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webforge\Common\PHPClass;
use Doctrine\Common\Annotations\AnnotationRegistry;

class BootContainer extends WebforgeBootContainer {

  /**
   * @var Webforge\Doctrine\Container
   */
  protected $doctrineContainer;
  
  /**
   * @var Webforge\ProjectStack\Symfony\Kernel
   */
  protected $kernel;
  
  public function __construct($rootDirectory) {
    parent::__construct($rootDirectory);
    $this->initProject($this->getProject());
  }

  /**
   * Gets a service from the dependency injection
   * 
   * @param string $id              The service identifier
   * @param int    $invalidBehavior The behavior when the service does not exist
   * @return object The associated service
   */
  public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
    return $this->getKernel()->getContainer()->get($id, $invalidBehavior);
  }

  protected function initProject(Project $project) {
    
  }

  protected function initKernel(Kernel $kernel) {
    $kernel->loadClassCache();
    $kernel->boot();
  }

  public function registerDoctrineAnnotations() {
    AnnotationRegistry::registerLoader(array($this->getAutoLoader(), 'loadClass'));
  }

  /**
   * @return Webforge\ProjectStack\Symfony\Kernel
   */
  public function getKernel() {
    if (!isset($this->kernel)) {
      $kernelClass = $this->getKernelClass()->getFQN();
      $this->kernel = new $kernelClass($this->project);
      $this->initKernel($this->kernel);
    }
    return $this->kernel;
  }

  protected function getKernelClass() {
    $kernelClass = new PHPClass('Kernel');
    $kernelClass->setNamespace($this->getProject()->getNamespace());

    if (class_exists($kernelClass->getFQN())) {
      return $kernelClass;
    } else {
      $kernelClass->setNamespace('Webforge\ProjectStack');
      return $kernelClass;
    }
  }
  
  /**
   * 
   * @deprecated use doctrine bundle to manage doctrine
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
   * @param Webforge\ProjectStack\Symfony\Kernel $kernel
   * @chainable
   */
  public function injectKernel(Kernel $kernel) {
    $this->kernel = $kernel;
  }

  public function resetKernel() {
    unset($this->kernel);
  }
  
  /**
   * @param Webforge\Doctrine\Container $doctrineContainer
   * @chainable
   */
  public function injectDoctrineContainer(DoctrineContainer $doctrineContainer) {
    $this->doctrineContainer = $doctrineContainer;
  }
}