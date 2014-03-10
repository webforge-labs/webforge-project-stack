<?php

namespace Webforge\ProjectStack\Symfony;

use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Webforge\Framework\Project;
use Webforge\Common\System\Dir;

class Kernel extends SymfonyKernel {

  protected $project;

  // with the first parameter we'll break for example insulate() in KernelTestCase from symfony
  public function __construct(Project $project, $status = NULL) {
    $this->project = $project;
    
    parent::__construct($status ?: $this->project->getStatus(), $debug = $this->project->isDevelopment());
  }

  public function registerBundles() {
    $bundles = array();

    $bundles[] = new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();

    if (class_exists('Symfony\Bundle\SecurityBundle\SecurityBundle')) {
      $bundles[] = new \Symfony\Bundle\SecurityBundle\SecurityBundle();
    }

    if (class_exists('Doctrine\Bundle\DoctrineBundle\DoctrineBundle')) {
      $bundles[] = new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle();
    }

    /*
      new Symfony\Bundle\TwigBundle\TwigBundle(),
      new Symfony\Bundle\MonologBundle\MonologBundle(),
      new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
      new Symfony\Bundle\AsseticBundle\AsseticBundle(),
      new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
      new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
    );

    */
    if (in_array($this->getEnvironment(), array('development', 'test'))) {
      //$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
      //$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
      $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
    }

    return $bundles;
  }

  public function getRootDir() {
    if (!isset($this->rootDir)) {
      $this->rootDir = $this->project->dir('root')->getPath(Dir::WITHOUT_TRAILINGSLASH);
    }

    return $this->rootDir;
  }

  public function getCacheDir() {
    return $this->project->dir('cache')->sub('symfony-'.$this->environment.'/')->getPath(Dir::WITHOUT_TRAILINGSLASH);
  }

  public function getLogDir() {
    return $this->project->dir('logs')->getPath(Dir::WITHOUT_TRAILINGSLASH);
  }

  public function registerContainerConfiguration(LoaderInterface $loader) {
    $loader->load((string) $this->project->dir('etc')->getFile('symfony/config_'.$this->getEnvironment().'.yml'));
  }
}
