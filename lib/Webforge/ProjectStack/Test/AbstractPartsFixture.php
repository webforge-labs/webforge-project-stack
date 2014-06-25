<?php

namespace Webforge\ProjectStack\Test;

use Webforge\ProjectStack\ObjectManagerAware;
use Doctrine\Common\Persistence\ObjectManager;

class AbstractPartsFixture implements PartsFixture, ObjectManagerAware {

  protected $em;
  protected $helper;

  public function setFixtureHelper(\Webforge\ProjectStack\Test\FixtureHelper $helper) {
    $this->helper = $helper;
  }

  public function setObjectManager(ObjectManager $om) {
    $this->em = $om;
  }

  protected function persist($object) {
    $this->em->persist($object);
    return $object;
  }
}
