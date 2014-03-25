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

  /**
   * Used for setting the environment in the kernel
   *
   * @var string
   */
  protected $environment;

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
    $container = $this->getKernel()->getContainer();

    if (!isset($container)) {
      throw new \Exception('Kernel is not bootet, yet');
    }

    return $container->get($id, $invalidBehavior);
  }

  protected function initProject(Project $project) {
    
  }

  protected function initKernel(Kernel $kernel) {
    $kernel->loadClassCache();
    $kernel->boot();
  }

  public function registerDoctrineAnnotations() {
    //AnnotationRegistry::registerLoader('class_exists');
    AnnotationRegistry::registerLoader(array($this->getAutoLoader(), 'loadClass'));
  }

  /**
   * @return Webforge\ProjectStack\Symfony\Kernel
   */
  public function getKernel() {
    if (!isset($this->kernel)) {
      $this->kernel = $this->createKernel();
    }
    return $this->kernel;
  }

  /**
   * @return Webforge\ProjectStack\Symfony\Kernel
   */
  public function createKernel($env = NULL) {
    $kernelClass = $this->getKernelClass()->getFQN();
    $kernel = new $kernelClass($env ?: $this->getEnvironment(), $this->getProject()->isDevelopment(), $this->project);
    $this->initKernel($kernel);

    return $kernel;
  }

  /**
   * @return string
   */
  public function getEnvironment() {
    return $this->environment ?: $this->getProject()->getStatus();
  }

  /**
   * Sets the enviroment for the container globally
   *
   * if this environment is set it is used for creating the kernel
   *
   * notice: if the kernel is already constructed, this has no effect
   * you have to shutdownAndResetKernel and create a new one (with getKernel)
   */
  public function setEnvironment($env) {
    $this->environment = $env;
  }

  /**
   * Shutsdown the current Kernel if existing and resets the internal Kernel to NULL
   * 
   * on the next getKernel() a new Kernel will be constructed from scratch
   */
  public function shutdownAndResetKernel() {
    if (isset($this->kernel)) {
      $this->kernel->shutdown();
    }

    $this->kernel = NULL;
  }

  protected function getKernelClass() {
    $kernelClass = new PHPClass('Kernel');
    $kernelClass->setNamespace($this->getProject()->getNamespace());

    if (class_exists($kernelClass->getFQN())) {
      return $kernelClass;
    } else {
      $kernelClass->setNamespace('Webforge\ProjectStack\Symfony');
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
