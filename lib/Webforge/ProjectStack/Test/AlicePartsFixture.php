<?php

namespace Webforge\ProjectStack\Test;

use stdClass;

class AlicePartsFixture extends AbstractPartsFixture {

  protected $project;
  protected $seed;
  protected $loader;

  public function __construct($project, $aliceSeed = NULL) {
    $this->project = $project;
    $this->seed = $aliceSeed;
    $this->providers = array();
  }

  public function getLoader() {
    if (!isset($this->loader)) {
      $this->loader = new \Nelmio\Alice\Loader\Yaml('de', $this->providers, $this->seed);
    }

    return $this->loader;
  }

  public function addProvider($provider) {
    $this->providers[] = $provider;
  }

  public function reset() {
  }

  public function loadFile($name) {
    $file = $this->project->dir('alice-fixtures')->getFile($name);

    $objects = $this->getLoader()->load((string) $file);

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
