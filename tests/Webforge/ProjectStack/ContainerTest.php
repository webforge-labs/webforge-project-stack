<?php

namespace Webforge\ProjectStack;

class ContainerTest extends \Webforge\Code\Test\Base {

  protected $projectContainer, $project;
  
  public function setUp() {
    parent::setUp();

    $this->projectContainer = $this->frameworkHelper->getBootContainer();
    $this->project = clone $this->projectContainer->getProject();
    $this->notConfiguredProject = clone $this->project;

    $cfg = $this->project->getConfiguration();
    $cfg->set(
      array('db'), 
      Array(
       'default'=>array(
         'user'=>'roadrunner',
         'password'=>'r0adrunn3r',
         'database'=>'blog'
       )
      )
    );

    $cfg->set(
      array('directory-locations'), 
      array(
        'doctrine-entities'=>'tests/files/Entities/',
        'doctrine-proxies'=>'files/cache/doctrine/proxies/'
      )
    );

    $this->project->configurationUpdate();
  }

  public function testReturnsTheDoctrineContainerWithConfigurationFromProject() {
    $this->assertInstanceOf('Webforge\Doctrine\Container', $dcc = $this->projectContainer->getDoctrineContainer());
    $this->assertInstanceOf('Doctrine\ORM\EntityManager', $dcc->getEntityManager('default'));

    $this->assertEquals(
      (string) $this->project->getRootDirectory()->sub('tests/files/Entities/'),
      $this->project->dir('doctrine-entities')
    );

    $this->assertEquals(
      (string) $this->project->getRootDirectory()->sub('files/cache/doctrine/proxies/'),
      (string) $this->project->dir('doctrine-proxies')
    );
  }

  public function testWithoutPaths_InitDoctrineSetsTheEntitiesPathToTheMainNamespaceEntitiesSub() {
    return "project injection not yet ready";
    $this->projectContainer = new BootContainer($this->notConfiguredProject);
    $dcc = $this->projectContainer->getDoctrineContainer();
    $dcc->getEntityManager();

    $this->assertEquals(
      (string) $this->notConfiguredProject->dir('lib')->sub('Webforge/ProjectStack/Entities/'),
      (string) $this->notConfiguredProject->dir('doctrine-entities')
    );

    $this->assertInstanceOf('Webforge\Common\System\Dir', $proxies = $this->notConfiguredProject->dir('doctrine-proxies'));
    $this->assertEquals($proxies, $dcc->getProxyDirectory());
  }

  public function testReturnsAKernel() {
    $this->markTestSkipped('no configuration (local) is made for testing');
    $this->assertInstanceOf('Symfony\Component\HttpKernel\KernelInterface', $this->projectContainer->getKernel());
  }
}
