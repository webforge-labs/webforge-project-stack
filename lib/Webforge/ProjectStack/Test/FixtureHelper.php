<?php

namespace Webforge\ProjectStack\Test;

use Doctrine\Common\DataFixtures\ReferenceRepository;

class FixtureHelper {

  protected $references;

  public function __construct(ReferenceRepository $references) {
    $this->references = $references;
  }

  public function getPartsReference($part, $referenceName) {
    return $this->getReference($part.'.'.$referenceName);
  }

  // If already existing throws a BadMethodCallException
  public function addPartsReference($part, $referenceName, $object) {
    $this->references->addReference($part.'.'.$referenceName, $object);
  }

  public function setReference($name, $object) {
    $this->references->setReference($name, $object);
  }

  public function addReference($name, $object) {
    $this->references->addReference($name, $object);
  }
    
  public function getReference($name) {
    return $this->references->getReference($name);
  }

  public function hasReference($name) {
    return $this->references->hasReference($name);
  }
}
