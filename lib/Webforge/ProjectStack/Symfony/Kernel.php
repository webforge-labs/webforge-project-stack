<?php

namespace Webforge\ProjectStack\Symfony;

use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webforge\Framework\Project;
use Webforge\Common\System\Dir;
use Webforge\ProjectStack\Symfony\DependencyInjection\FixturesCompilerPass;
use Webforge\Common\String as S;

class Kernel extends SymfonyKernel {

  protected $project;

  public function __construct($environment, $debug, Project $project = NULL) {
    $this->project = $project ?: $GLOBALS['env']['container']->getProject();
    
    parent::__construct($environment, $debug);
  }

  public function registerBundles() {
    $bundles = array();

    $bundles[] = new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
    $bundles[] = new \JMS\SerializerBundle\JMSSerializerBundle();

    if (class_exists('Symfony\Bundle\SecurityBundle\SecurityBundle')) {
      $bundles[] = new \Symfony\Bundle\SecurityBundle\SecurityBundle();
    }

    if (class_exists('Doctrine\Bundle\DoctrineBundle\DoctrineBundle')) {
      $bundles[] = new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle();
    }

    if (class_exists('Symfony\Bundle\MonologBundle\MonologBundle')) {
      $bundles[] = new \Symfony\Bundle\MonologBundle\MonologBundle();
    }

    if (class_exists('Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle')) {
      $bundles[] = new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle();
    }

    if (class_exists('Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle')) {
      $bundles[] = new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle();
    }

    if (class_exists('Symfony\Bundle\TwigBundle\TwigBundle')) {
      $bundles[] = new \Symfony\Bundle\TwigBundle\TwigBundle();
    }
    
    if (mb_strpos($this->getEnvironment(), 'development') !== FALSE) {
      
      if (class_exists('Symfony\Bundle\WebProfilerBundle\WebProfilerBundle')) {
        $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
      }

      $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
    }

    return $bundles;
  }

  protected function getEnvParameters() {
    $project = $this->project;

    $env = parent::getEnvParameters();

    // please note: this is executed BEFORE the config is loaded, so these parameters are just a default
    // => everything is overwritable by the config.yml
    return array_merge(
      $env,
      array(
        'webforge.project.namespace' => $project->getNamespace(),
        'webforge.project.name' => $project->getName(),
        'webforge.project.lower-name' => $project->getLowerName(),
        'webforge.host' => $project->getHost(),
        'webforge.project.base-url' => (string) $project->getHostUrl(),
        'webforge.project.cms-base-url' => (string) $project->getHostUrl('cms'),
        'webforge.project.directory-locations.doctrine-entities' => $project->dir('doctrine-entities')->wtsPath(),
        'router.request_context.host'=>$project->getHostUrl()->getHost(),
        'in_tests' => S::endsWith($this->getEnvironment(), '_in_tests')
      )
    );
  }

  protected function prepareContainer(ContainerBuilder $container) {
    $r = parent::prepareContainer($container);

    $container->addCompilerPass(new FixturesCompilerPass());

    return $r;
  }

  public function getRootDir() {
    if (!isset($this->rootDir)) {
      $this->rootDir = $this->project->dir('root')->getPath(Dir::WITHOUT_TRAILINGSLASH);
    }

    return $this->rootDir;
  }

  public function getCacheDir() {
    return $this->project->dir('cache')->sub('symfony-'.$this->getEnvironment().'/')->getPath(Dir::WITHOUT_TRAILINGSLASH);
  }

  public function getLogDir() {
    return $this->project->dir('logs')->getPath(Dir::WITHOUT_TRAILINGSLASH);
  }

  public function registerContainerConfiguration(LoaderInterface $loader) {
    $loader->load((string) $this->project->dir('etc')->getFile('symfony/config_'.$this->getEnvironment().'.yml'));

    $hostConfigFile = $this->project->dir('etc')->getFile('symfony/config_'.$this->project->getHost().'.yml');
    if ($hostConfigFile->exists()) {
      $loader->load((string) $hostConfigFile);
    }
  }
}
