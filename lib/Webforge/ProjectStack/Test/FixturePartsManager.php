<?php

namespace Webforge\ProjectStack\Test;

class FixturePartsManager {

  protected $matcher;
  protected $manager;

  public function __construct(FixturePartsMatcher $matcher, $manager) {
    $this->matcher = $matcher;
    $this->manager = $manager;
  }

  public function given($partDescription) {
    try {
      $this->calls[] = $this->matcher->match($partDescription);

      return $this;
    } catch (FixturePartMismatchException $e) {
      throw $e;
    }
  }

  public function execute() {
    $this->manager->resetFixtures();

    foreach ($this->calls as $call) {
      list($fixture, $method, $params) = $call;

      if ($method instanceof \Closure) {
        call_user_func_array($method, array_merge(array($fixture), $params));
      } else {
        call_user_func_array(array($fixture, $method), $params);
      }

      $this->manager->add($fixture);
    }

    return $this->manager->execute();
  }
}
