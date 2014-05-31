<?php

namespace Webforge\ProjectStack;

use Doctrine\Common\Persistence\ObjectManager;

interface ObjectManagerAware {

  public function setObjectManager(ObjectManager $om);
  
}
