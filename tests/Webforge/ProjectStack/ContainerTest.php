<?php

namespace Webforge\ProjectStack;

class ContainerTest extends \Webforge\Code\Test\Base {

  protected $projectContainer, $project;
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\Container';
    parent::setUp();

    $this->project = clone $this->frameworkHelper->getBootContainer()->getProject();
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
    $this->projectContainer = new Container($this->project);
  }

  public function testReturnsTHeProjectConnected() {
    $this->assertSame($this->project, $this->projectContainer->getProject());
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
    $this->projectContainer = new Container($this->notConfiguredProject);
    $dcc = $this->projectContainer->getDoctrineContainer();
    $dcc->getEntityManager();

    $this->assertEquals(
      (string) $this->notConfiguredProject->dir('lib')->sub('Webforge/ProjectStack/Entities/'),
      (string) $this->notConfiguredProject->dir('doctrine-entities')
    );

    $this->assertInstanceOf('Webforge\Common\System\Dir', $proxies = $this->notConfiguredProject->dir('doctrine-proxies'));
    $this->assertEquals($proxies, $dcc->getProxyDirectory());
  }
}
