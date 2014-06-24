<?php

namespace Webforge\ProjectStack;

class BootContainerTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\BootContainer';
    parent::setUp();

    $this->projectRoot = $this->getTestDirectory('acme-blog/');
    $this->autoLoader = $this->getMock('Composer\Autoload\ClassLoader');

    $this->bootContainer = new BootContainer($this->projectRoot->wtsPath());
    $this->bootContainer->setAutoLoader($this->autoLoader);
    $this->bootContainer->init();
  }

  public function testItsBootedWithTheProjectInDirectory() {
    $this->markTestSkipped('fix webforge to assert this');
    // this would be cool, but webforge is doing something wrong here:
    // it does not search for the composer.json in the current directory. it just sees that this directory is a subdirectory from projectstack and loads this
    $this->assertEquals('acme-blog', $this->bootContainer->getProject()->getName());
    
  }

  public function testEnablingTestEnvironment() {
    $this->bootContainer->enableTestEnvironment();

    $this->assertStringEndsWith('_in_tests', $this->bootContainer->getEnvironment());
  }
}
