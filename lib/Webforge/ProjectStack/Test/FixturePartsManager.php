<?php

namespace Webforge\ProjectStack\Test;

use Webforge\Doctrine\Fixtures\FixturesManager;
use Webforge\Common\String as S;
use InvalidArgumentException;

class FixturePartsManager {

  protected $manager;

  protected $fixtures = array();

  public function __construct(FixturesManager $manager) {
    $this->manager = $manager;
  }

  /**
   * @param string $fixtureName
   * @param string $method
   * @param array $params
   */
  public function load($fixtureName, $method, array $params = array()) {
    $this->calls[] = $this->validateCall($fixtureName, $method, $params);

    return $this;
  }

  /**
   * Adds a part as a file of an alice fixture
   * 
   * @param string $filename the filename without .yml in the project->dir('alice-fixtures') directory
   */
  public function alice($filename) {
    $this->load('alice', 'loadFile', array(S::expand($filename, '.yml', S::END)));

    return $this;
  }

  public function reset() {
    $this->calls = array();
  }

  /**
   * Truncates the DB, Executes all queued parts and flushes the entity manager
   */
  public function execute() {
    $this->manager->resetFixtures();

    /*
    $groupedCalls = array();
    // eleminate duplicates, but this will keep the original order of the calls within one fixture
    foreach ($this->calls as $call) {
      list($partsFixture, $method, $params) = $call;
      $hash = spl_object_hash($partsFixture);

      if (!isset($groupedCalls[$hash])) {
        $groupedCalls[$hash] = array();
      }

      $groupedCalls[$hash][$method] = $call;
    }
    */
    $this->log = "Adding fixtures\n";
    $this->manager->add($calls = new CallsPartsFixture($this->calls));

    $this->log .= "Starting to execute parts:\n";
    $this->manager->execute();
    $this->log .= $calls->log;

    return $this;
  }

  public function debug() {
    print $this->log;
    return $this;
  }

  public function setPartsFixture($key, $fixture) {
    if (!is_object($fixture)) {
      throw new InvalidArgumentException('second parameter for setFixture has to be an fixture Object');
    }
    $this->fixtures[$key] = $fixture;
  }

  private function fixture($name) {
    if (!array_key_exists($name, $this->fixtures)) {
      throw new InvalidArgumentException(
        sprintf(
          "There is no fixture defined with name: '%s'. There are only: %s\n".
          "Suggestion: create a service and tag it with:\n".
          "      tags:\n".
          "        -  {  name: projectstack.partsfixture, fixtureName: %1\$s }\n",
        $name, 
        implode(array_keys($this->fixtures))
       )
      );
    }

    return $this->fixtures[$name];
  }


  private function validateCall($fixtureName, $method, array $params = array()) {
    $fixture = $this->fixture($fixtureName);

    $callback = array($fixture, $method);

    if (!is_callable($callback)) {
      throw new InvalidArgumentException(
        sprintf(
          'the callback for fixture "%s" matched: Object<%s> -> %s() but it is not callable.',
          $fixtureName, get_class($callback[0]), $method
        )
      );
    }

    return array($fixture, $method, $params);
  }
}
