<?php

namespace Webforge\ProjectStack\Test;

class Base extends \Webforge\Code\Test\Base {

  protected $em;
  protected $helper;

  protected static $fixturesExecuted = FALSE;

  public static function setUpBeforeClass() {
    self::$fixturesExecuted = FALSE;
  }

  protected function initHelper() {
    $this->helper = new Helper($this->getContainer());
    $this->helper->onTestSetup();
    $this->em = $this->helper->em;
  }

  protected function resetAndBootKernel(Array $options = array()) {
    $container = $this->frameworkHelper->getBootContainer();

    $container->shutdownAndResetKernel();

    $container->injectKernel(
      $container->createKernel(isset($options['environment']) ? $options['environment'] : NULL)
    );

    return $container->getKernel(); 
  }

  protected function assertJsonResponse($response, $code = 200) {
    try {
      $this->assertSymfonyResponse($response)
        ->code($code)
        ->format('json');
    } catch (\Webforge\Code\Test\SymfonyAssertion $e) {
      print $response->getContent();
      throw $e;
    }

    $content = (string) $response->getContent();

    if (empty($content)) return NULL;

    return $this->assertThatObject($this->parseJSON($content));
  }

  protected function jsonRequest($method, $url, $data = array()) {
    return $this->client->request(
      $method, 
      $url,
      array(),
      array(),
      array(
        'HTTP_ACCEPT'  => 'application/json',
        'CONTENT_TYPE' => 'application/json'
        ),
      // make sure that no invalid json can be passed
      is_string($data) ? json_encode($this->parseJSON($data)) : json_encode($data)
    );
  }

  protected function executeFixtures(Array $fixtures, array $options = array()) {
    $once = isset($options['once']) ? (bool) $options['once'] : FALSE;

    if (!$once || ($once && !self::$fixturesExecuted)) {
      $this->helper->executeFixtures($fixtures);
      self::$fixturesExecuted = TRUE;
    }
  }

  protected function executeFixturesAgain() {
    self::$fixturesExecuted = FALSE;
  }

  protected function getKernel() {
    return $this->frameworkHelper->getBootContainer()->getKernel();
  }

  protected function getContainer() {
    return $this->getKernel()->getContainer();
  }

  protected function get($serviceId) {
    return $this->frameworkHelper->getBootContainer()->get($serviceId);
  }
}
