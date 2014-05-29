<?php

namespace Webforge\ProjectStack\Test;

use Doctrine\Common\Persistence\ObjectManager;
use stdClass;

class DefaultPartsFixture extends \Doctrine\Common\DataFixtures\AbstractFixture {

  protected $userManager;

  protected $users;

  public $fixtureFiles = array();

  public function __construct($container) {
    $this->userManager = $container->get('fos_user.user_manager');
    $this->project = $container->get('projectstack.project');
  }

  public function load(ObjectManager $em) {
    $loader = new \Nelmio\Alice\Loader\Yaml('de', array($this), $seed = 7);
    $processors = array(new FOSUserProcessor($this->userManager));

    foreach ($this->fixtureFiles as $file) {
      $objects = $loader->load((string) $file);

      foreach ($objects as $key=>$object) {
        foreach ($processors as $processor) {
          $object = $processor->preProcess($object);
          $object = $processor->postProcess($object);

          $objects[$key] = $object;

          $em->persist($object);
        }
      }
    }

    $em->flush();
  }

  public function addAliceFixtureFile($name) {
    $this->fixtureFiles[] = $this->project->dir('alice-fixtures')->getFile($name);
  }
}
