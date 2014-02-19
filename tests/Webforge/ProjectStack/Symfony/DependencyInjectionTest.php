<?php

namespace Webforge\ProjectStack\Symfony;

use Webforge\Common\System\Dir;

class DependencyInjectionTest extends \Webforge\Code\Test\Base {

  public function setUp() {
    parent::setUp();

    $this->markTestSkipped('no configuration (local) is made for testing');
    $this->kernel = new Kernel($this->project = $this->frameworkHelper->getProject());
    $this->kernel->boot();

    $this->container = $this->kernel->getContainer();
  }


  /**
   * @dataProvider provideClassesOfInstances
   */
  public function testClassesOfInstances($serviceName, $fqn) {
    $this->assertInstanceOf($fqn, $this->container->get($serviceName), $fqn);
  }
  
  public static function provideClassesOfInstances() {
    $tests = array();
  
    $test = function() use (&$tests) {
      $tests[] = func_get_args();
    };
  
    $test('projectstack.serializer', 'JMS\Serializer\Serializer');
    //$test('projectstack.entitymanager', 'Doctrine\ORM\EntityManager');
  
    return $tests;
  }
}
