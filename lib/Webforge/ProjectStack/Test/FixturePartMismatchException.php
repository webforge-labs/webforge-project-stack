<?php

namespace Webforge\ProjectStack\Test;

class FixturePartMismatchException extends \RuntimeException {

  public function __construct($part) {
    parent::__construct(sprintf('Cannot match part: "%s"', $part));
  }
}
