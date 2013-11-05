<?php

namespace Webforge\ProjectStack\Symfony\Http;

use Symfony\Component\HttpFoundation\Response;
use Webforge\Common\JS\JSONConverter;

class ResponseBuilder {

  protected $body;
  protected $code = 200;
  protected $headers = array();

  public static function create() {
    return new static();
  }

  public function code($num) {
    $this->code = $num;
    return $this;
  }

  public function html($body = NULL) {
    if (func_num_args() > 0) {
      $this->body = $body;
    }

    $this->headers['content-type'] = 'text/html';
    
    return $this->build();
  }

  public function json($body) {
    if (func_num_args() > 0) {
      $this->body = $this->encodeJSON($body);
    }

    $this->headers['content-type'] = 'application/json';

    return $this->build();
  }

  protected function encodeJSON($json) {
    if (!is_string($json)) {
      $json = JSONConverter::create()->stringify($json);
    }
    return $json;
  }

  /**
   * @return Symfony\Component\HttpFoundation\Response
   */
  public function build() {
    return new Response($this->body, $this->code, $this->headers);
  }
}
