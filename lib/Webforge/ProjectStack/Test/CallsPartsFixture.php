<?php

namespace Webforge\ProjectStack\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Webforge\ProjectStack\ObjectManagerAware;
use Webforge\Common\String as S;

class CallsPartsFixture extends \Doctrine\Common\DataFixtures\AbstractFixture {

  protected $calls;
  public $log;

  public function __construct(Array $calls) {
    $this->calls = $calls;
  }

  public function load(ObjectManager $om) {
    $this->log = '';
    $fixtureHelper = new FixtureHelper($this->referenceRepository);

    foreach ($this->calls as $call) {
      list($fixture, $method, $params) = $call;

      if ($fixture instanceof ObjectManagerAware) {
        $fixture->setObjectManager($om);
      }

      if ($fixture instanceof \Doctrine\Common\DataFixtures\SharedFixtureInterface) {
        $fixture->setReferenceRepository($this->referenceRepository);
      }

      if ($fixture instanceof PartsFixture) {
        $fixture->setFixtureHelper($fixtureHelper);
      }

      $this->executeCall($fixture, $method, $params);
    }

    $this->log .= "flushing object manager\n";
    $om->flush();
  }

  private function executeCall($fixture, $method, array $params = array()) {

    if ($method instanceof \Closure) {
      $this->log .= sprintf("Calling closure for fixture: '%s'\n", get_class($fixture));
      $this->log .= S::indent(call_user_func_array($method, array_merge(array($fixture), $params)), 2);
    } else {
      $this->log .= sprintf("Calling: %s->%s()\n", get_class($fixture), $method);
      $this->log .= S::indent(call_user_func_array(array($fixture, $method), $params), 2);
    }
  }
}
