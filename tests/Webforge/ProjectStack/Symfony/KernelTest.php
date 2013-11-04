<?php

namespace Webforge\ProjectStack\Symfony;

use Webforge\Common\System\Dir;

class KernelTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\Kernel';
    parent::setUp();

    $this->kernel = new Kernel($this->project = $this->frameworkHelper->getProject());
  }

  public function testImplementsSymfonyKernel() {
    $this->assertInstanceOf('Symfony\Component\HttpKernel\KernelInterface', $this->kernel);
  }

  public function testRootDefaultsToProjectRoot() {
    $this->assertEquals(
      $this->project->dir('root')->wtsPath(),
      $this->kernel->getRootDir()
    );
  }

  public function testCacheIsInCacheRoot() {
    $cacheDir = Dir::factoryTS($cache = $this->kernel->getCacheDir());

    $this->assertTrue(
      $cacheDir->isSubdirectoryOf($this->project->dir('cache')),
      $cache.' should be sub of '.$this->project->dir('cache')
    );
  }

  public function testLogsIsDefined() {
    $this->assertNotEmpty($lg = $this->kernel->getLogDir());
    $this->assertInternalType('string', $lg);
  }
}
