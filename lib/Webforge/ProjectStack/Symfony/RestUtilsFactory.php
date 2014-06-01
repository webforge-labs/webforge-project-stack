<?php

namespace Webforge\ProjectStack\Symfony;

class RestUtilsFactory {

  protected $em;

  public function __construct($em) {
    $this->em = $em;
  }

  public function getRestUtils($fqn, $plural, $singular) {
    return new RestUtils($fqn, $plural, $singular, $this->em);
  }
}
