<?php

namespace Webforge\ProjectStack\Symfony\Http;

use Symfony\Component\HttpFoundation\Response;

class ResponseBuilder {

  protected $content;
  protected $code = 200;
  protected $headers = array();

  public static function create() {
    return new static();
  }

  public function code($num) {
    $this->code = $num;
    return $this;
  }

  public function content($content) {
    $this->content = $content;
    return $this;
  }

  public function html($content = NULL) {
    if (func_num_args() > 0) {
      $this->content = $content;
    }

    $this->headers['content-type'] = 'text/html';
    
    return $this->build();
  }

  /**
   * @return Symfony\Component\HttpFoundation\Response
   */
  public function build() {
    return new Response($this->content, $this->code, $this->headers);
  }
}
