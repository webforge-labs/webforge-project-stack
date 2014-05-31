<?php

namespace Webforge\ProjectStack\Test;

use Webforge\Common\String as S;
use Webforge\Common\Exception;
use Webforge\Code\Test\GuzzleTester;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Client as TestClient;
use Webforge\Common\Preg;
use Webforge\Common\JS\JSONConverter;

/**
 * A Test helper for both base classes, for preferring aggregation over inheritation
 */
class Helper {

  public $em;
  protected $fm;
  protected $container;
  protected $dc;

  protected $fixtureParts;

  protected $entitiesNS;

  public function __construct($container) {
    $this->container = $container;
    $this->project = $container->get('projectstack.project');
    $this->entitiesNS = $this->project->getNamespace().'\\Entities';
    $this->fixtureParts = $container->get('projectstack.fixtures.parts_manager');
    $this->dc = $container->get('doctrine');
    $this->em = $this->dc->getManager();
    $this->url = $container->getParameter('webforge.project.base-url');
    $this->stagingToken = $container->getParameter('staging_access_token');
  }

  public function hydrate($entityName, $criteria) {
    if (!is_array($criteria)) {
      $criteria = array('id'=>$criteria);
    }

    $entity = $this->getRepository($entityName)->findOneBy($criteria);

    if (!is_object($entity)) {
      throw new Exception('Cannot find '.$entityName.' with '.json_encode($criteria));
    }

    return $entity;
  }

  public function createGuzzleTester() {
    $tester = new GuzzleTester($this->url);
    $stagingToken = $this->stagingToken;

    $tester->getClient()->getEventDispatcher()->addListener('client.create_request', function (\Guzzle\Common\Event $e) use ($stagingToken) {
      $e['request']->addCookie('staging_access', $stagingToken); // siehe index.php
    });

    return $tester;
  }

  public function setStagingCookie(TestClient $client) {
    $client->getCookieJar()->set(
      new Cookie('staging_access', $this->stagingToken)
    );
  }

  public function onTestSetup() {
    $this->em->clear();
    $this->resetEntityManager();
  }

  public function resetEntityManager() {
    if (!$this->em->isOpen()) {
      echo "resetting entity manager\n";
      //$this->em->getConnection();
      $this->dc->resetManager($this->emName);
      $this->em = $this->dc->getManager($this->emName);
    }
  }

  public function save($entity) {
    $this->em->persist($entity);
    $this->em->flush();
  }

  /**
   * Attention that clears the em
   */
  public function refresh($entity) {
    $this->em->clear();

    return $this->hydrate(get_class($entity), $entity->getId());
  }

  public function getRepository($entityName) {
    $entityName = ucfirst($entityName);
    $entityFQN = S::expand($entityName, $this->entitiesNS.'\\', S::START);
    return $this->em->getRepository($entityFQN);
  }

  public function fixtureParts() {
    $this->fixtureParts->reset();

    return $this->fixtureParts;
  }

  public function executeFixtures(array $fixtures) {
    $this->fm = $this->container->get('projectstack.fixtures_manager');
    $this->fm->resetFixtures();
    
    foreach ($fixtures as $fixture) {
      $this->fm->add($this->container->get('fixtures.'.$fixture));
    }

    return $this->fm->execute();
  }
}
