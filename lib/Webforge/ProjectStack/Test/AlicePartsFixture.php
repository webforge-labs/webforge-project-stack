<?php

namespace Webforge\ProjectStack\Test;

use stdClass;

class AlicePartsFixture extends AbstractPartsFixture {

  public function __construct($project) {
    $this->project = $project;
    $this->loader = new \Nelmio\Alice\Loader\Yaml('de', array($this), $seed = 7);
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
