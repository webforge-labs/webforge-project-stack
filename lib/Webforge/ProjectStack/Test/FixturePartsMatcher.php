<?php

namespace Webforge\ProjectStack\Test;

use Webforge\Common\Preg;

class FixturePartsMatcher {

  protected $fixtures;

  /**
   * @param array $fixtures the key is the name returned from the matcher and the value is the fixture to be used in the manager
   */
  public function __construct(array $fixtures) {
    $this->fixtures = $fixtures;

    $this->rx = array(
      'provider'=>array(
        'rx'=>'/^([a-zA-Z]+[-a-zA-Z0-9_]*):\s*"([^"]+)"\s*$/',
        'ls'=>array(1,2),
        'callable'=>array($this, 'provider')
      )
    );
  }

  /**
   * 
   * @param string $part the part as a sentence that describes the fixture part
   * @return list($fixture, $method, Array $parameters)
   */
  public function match($part) {
    foreach ($this->rx as $matcher) {
      if ($match = Preg::qmatch($part, $matcher['rx'], $matcher['ls'])) {
        return call_user_func_array($matcher['callable'], $match);
      }
    }

    throw new FixturePartMismatchException($part);
  }

  /**
   * internal matcher: "provider"
   * 
   *   <partName>: <key>
   * => calls a method named <partName>Provider with parameter <key> on the fixture
   */
  protected function provider($name, $key) {
    $fixture = $this->fixtures['default'];

    return array($fixture, 'addAliceFixtureFile', array($name.'.'.$key.'.yml'));
  }
}
