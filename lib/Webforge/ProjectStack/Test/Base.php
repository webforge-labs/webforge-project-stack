<?php

namespace Webforge\ProjectStack\Test;

class Base extends \Webforge\Code\Test\Base {

  protected $em;
  protected $helper;

  protected static $conn;

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

    self::$conn = $container->getKernel()->getContainer()->get('doctrine.dbal.default_connection');

    $container->shutdownAndResetKernel();

    $container->injectKernel(
      $container->createKernel(isset($options['environment']) ? $options['environment'] : NULL)
    );

    $kernel = $container->getKernel();

    // reuse dbal connection
    $kernel->getContainer()->set('doctrine.dbal.default_connection', self::$conn);

    return $kernel;
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

  protected function jsonRequest($method, $url, $data = array(), $headers = array()) {
    return $this->client->request(
      $method, 
      $url,
      array(),
      array(),
      array(
        'HTTP_ACCEPT'  => 'application/json',
        'CONTENT_TYPE' => 'application/json'
      ) + $headers,
      // make sure that no invalid json can be passed
      is_string($data) ? json_encode($this->parseJSON($data)) : json_encode($data)
    );
  }

  protected function htmlRequest($method, $url, $data = array(), $headers = array()) {
    return $this->client->request(
      $method, 
      $url,
      array(),
      array(),
      array(
        'HTTP_ACCEPT'  => 'text/html'
        // let symfony set content type?
      ) + $headers,
      $data // dont know the format, yet
    );
  }

  protected function executeFixtures(Array $fixtures, array $options = array()) {
    $once = isset($options['once']) ? (bool) $options['once'] : FALSE;

    if (!$once || ($once && !self::$fixturesExecuted)) {
      $this->helper->executeFixtures($fixtures);
      self::$fixturesExecuted = TRUE;
    }
  }

  public function executeOnce(FixturePartsManager $fixturesPartsManager) {
    if (!self::$fixturesExecuted) {
      $ret = $fixturesPartsManager->execute();

      self::$fixturesExecuted = TRUE;

      return $ret;
    }
  }


  protected function executeFixturesAgain() {    
    self::$fixturesExecuted = FALSE;
  }

  protected function resetDatabaseOnNextTest() {
    $this->executeFixturesAgain();
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
