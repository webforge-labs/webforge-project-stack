<?php

namespace Webforge\ProjectStack\Symfony\Http;

class ResponseBuilderTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\ResponseBuilder';
    parent::setUp();
  }

  protected function response() {
    return ResponseBuilder::create();
  }

  public function testResponseForHTML() {
    $response = $this->response()->code(304)->html('html-body');

    $this->assertSymfonyResponse($response)
      ->body('html-body')
      ->code(304)
      ->format('html');
  }
}
