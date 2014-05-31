<?php

namespace Webforge\ProjectStack\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Webforge\ProjectStack\ObjectManagerAware;
use stdClass;

class AlicePartsFixture implements ObjectManagerAware {

  protected $om;

  public function __construct($project) {
    $this->project = $project;
    $this->loader = new \Nelmio\Alice\Loader\Yaml('de', array($this), $seed = 7);
  }

  public function loadFile($name) {
    $file = $this->project->dir('alice-fixtures')->getFile($name);

    $objects = $this->loader->load((string) $file);

    $log = 'load alice file: '.(string) $file."\n";
    $log .= "  objects:\n";
    foreach($objects as $key=>$object) {
      $log .= "    $key\n";
      $this->om->persist($object);
    }
    $log .= "\n";

    return $log;
  }


  public function setObjectManager(ObjectManager $om) {
    $this->om = $om;
  }
}
