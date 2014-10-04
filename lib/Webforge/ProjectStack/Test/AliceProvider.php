<?php

namespace Webforge\ProjectStack\Test;

class AliceProvider {

  public function webforgeDateTime($string) {
    return new \Webforge\Common\DateTime\DateTime($string);
  }

}
