<?php

namespace Webforge\ProjectStack\Test;

interface PartsFixture {

  public function setFixtureHelper(\Webforge\ProjectStack\Test\FixtureHelper $helper);

  /**
   * Gets called before parts are loaded
   * 
   * when parts fixtures are stored in the container this is helpful to reset state of the parts fixture
   */
  public function reset();
}
