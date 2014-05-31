<?php

namespace Webforge\ProjectStack\Test;

use Webforge\Code\Test\HTMLTesting;

class KernelTestCase extends Base implements HTMLTesting {
  
  private $client, $authClient;

  protected function setUpAuthClient($user, $password, array $options = array(), array $server = array()) {
    $this->authClient = array($user, $password, $options, $server);
  }

  protected function setUpClient(array $options = array(), array $server = array()) {
    $this->client = array($options, $server);
  }

  public function setUp() {
    parent::setUp();

    if (is_array($this->authClient)) {
      // THIS RESETS THE CONTAINER and KERNEL
      $this->client = $this->createAuthClient($this->authClient[0], $this->authClient[1], $this->authClient[2], $this->authClient[3]);
    } elseif (is_array($this->client)) {
      $this->client = $this->createClient($this->client[0], $this->client[1]);
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

    $server = $this->augmentServerVariables($server);

    $client = $this->get('test.client');
    $client->setServerParameters($server);

    return $client;
  }

  private function augmentServerVariables($original, array $extenders = array()) {
    return array_merge(
      $original, 
      /*
       this https://github.com/symfony/symfony/blob/master/src/Symfony/Component/BrowserKit/Client.php 
       is overwriting the HOST parameter (which is uncool somehow...)

       we fix this here
      */
      array('HTTP_HOST'=>$this->getContainer()->getParameter('router.request_context.host')),
      $extenders
    );
  }

  protected function createAuthClient($user, $password, array $options = array(), array $server = array()) {
    $server = $this->augmentServerVariables($server, array(
      'PHP_AUTH_USER' => $user,
      'PHP_AUTH_PW'   => $password,
    ));

    $client = $this->createClient($options, $server);


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
