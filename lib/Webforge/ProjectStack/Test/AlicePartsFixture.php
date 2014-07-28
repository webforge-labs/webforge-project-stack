<?php

namespace Webforge\ProjectStack\Test;

use stdClass;

class AlicePartsFixture extends AbstractPartsFixture {

  protected $project;
  protected $seed;

  public function __construct($project, $aliceSeed = NULL) {
    $this->project = $project;
    $this->seed = $aliceSeed;
    $this->loader = new \Nelmio\Alice\Loader\Yaml('de', array($this), $this->seed);
  }

  public function reset() {
  }

  public function loadFile($name) {
    $file = $this->project->dir('alice-fixtures')->getFile($name);

    $objects = $this->loader->load((string) $file);

    $log = 'load alice file: '.(string) $file."\n";
    $log .= "  objects:\n";
    foreach($objects as $key => $object) {
      $log .= "    $key\n";
      $this->persist($object);
      $this->helper->addPartsReference('alice', $key, $object);
    }
    $log .= "\n";

    return $log;
  }
}
