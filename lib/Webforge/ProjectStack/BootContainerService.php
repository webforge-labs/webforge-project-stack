<?php

namespace Webforge\ProjectStack;

class BootContainerService {

  public function getBootContainer() {
    return $GLOBALS['env']['container'];
  }
}
