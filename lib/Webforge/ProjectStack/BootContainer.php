<?php

namespace Webforge\ProjectStack;

use Webforge\Framework\Project;
use InvalidArgumentException;
use Webforge\ProjectStack\Symfony\Kernel;
use Webforge\Setup\BootContainer as WebforgeBootContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webforge\Common\PHPClass;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\Types\Type as DBALType;
use Webforge\Common\String as S;

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
  }

  public function registerGlobal() {
    $GLOBALS['env']['container'] = $this;
    $GLOBALS['env']['root'] = $this->rootDirectory;
  }

  public function init() {
    $this->initProject($this->getProject());
    $this->initDoctrine();
    $this->initEnvironment();
  }

  public function initEnvironment() {
    /* set the environment from deploy-info.json (if this exists) */
    $deployInfo = $this->getWebforge()->getDeployInfo($this->getProject());

    if (isset($deployInfo->environment)) {
      $this->setEnvironment($deployInfo->environment);
    }

    if (defined('phpunit')) { // see phpunit.xml.dist
      $this->enableTestEnvironment();
    }
  }

  protected function initProject(Project $project) {
    
  }

  public function enableTestEnvironment() {
     if (!S::endsWith($this->getEnvironment(), 'in_tests')) {
       $this->setEnvironment($this->getEnvironment().'_in_tests');
     }
  }

  protected function initKernel(Kernel $kernel) {
    $kernel->loadClassCache();
    $kernel->boot();
  }

  public function initDoctrine() {
    $this->registerDoctrineAnnotations();

    $types = array(
     'WebforgeDateTime'=>'Webforge\Doctrine\Types\DateTimeType',
     'WebforgeDate'=>'Webforge\Doctrine\Types\DateType'
    );

    foreach ($types as $name => $class) {
      if (!DBALType::hasType($name)) {
        DBALType::addType($name, $class);
      }
    }
  }

  public function registerDoctrineAnnotations() {
    //AnnotationRegistry::registerLoader('class_exists');
    AnnotationRegistry::registerLoader(array($this->getAutoLoader(), 'loadClass'));
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

    $this->resetKernel();
  }

  public function resetKernel() {
    unset($this->kernel);
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
   * @param Webforge\ProjectStack\Symfony\Kernel $kernel
   * @chainable
   */
  public function injectKernel(Kernel $kernel) {
    $this->kernel = $kernel;
  }
  
}
