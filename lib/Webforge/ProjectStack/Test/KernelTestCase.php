<?php

namespace Webforge\ProjectStack\Test;

use Webforge\Code\Test\HTMLTesting;

class KernelTestCase extends Base implements HTMLTesting {
  
  protected $client;

  protected function setUpAuthClient($user, $password) {
    $this->client = array($user, $password);
  }

  public function setUp() {
    parent::setUp();

    if (is_array($this->client)) {
      // THIS RESETS THE CONTAINER and KERNEL
      $this->client = $this->createAuthClient($this->client[0], $this->client[1]);
    }

    $this->initHelper();
  }

 /**
   * Creates a Client.
   *
   * @param array $options An array of options to pass to the createKernel class
   * @param array $server  An array of server parameters
   *
   * @return Client A Client instance
   */
  protected function createClient(array $options = array(), array $server = array()) {
    $this->resetAndBootKernel($options);

    if (!class_exists('Symfony\Component\BrowserKit\Client')) {
      throw new \Exception("Symfony Browser Kit needs to be installed\ncomposer require --dev symfony/browser-kit:2.4.*");
    }

    $client = $this->get('test.client');
    $client->setServerParameters($server);

    return $client;
  }

  protected function createAuthClient($user, $password, array $options = array(), array $server = array()) {
    $client = $this->createClient($options, array_merge($server, array(
      'PHP_AUTH_USER' => $user,
      'PHP_AUTH_PW'   => $password,
    )));


    $client->followRedirects(); // follow the login redirect

    return $client;
  }

  public function assertSymfonyResponse($response = NULL) {
    return parent::assertSymfonyResponse($response ?: $this->client->getResponse());
  }


  protected function assertJsonResponse($response = NULL, $code = 200) {
    return parent::assertJsonResponse($response ?: $this->client->getResponse(), $code);
  }
}
