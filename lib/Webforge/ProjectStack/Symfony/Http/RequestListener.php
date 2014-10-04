<?php

namespace Webforge\ProjectStack\Symfony\Http;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Webforge\Common\DateTime\DateTime;

class RequestListener {

  protected $inTests;
  protected $options;

  public function __construct(\DateTime $now, $options) {
    $this->now = $now;
    $this->options = $options;
  }

  public function onKernelRequest(GetResponseEvent $event) {
    $request = $event->getRequest();

    if (isset($this->options['now'])) {
      $now = $request->headers->get($this->options['now']);

      //$this->logger->info(sprintf('investigating phase: %s, now: %s', $phase, $now));

      if ($now) {
      //$this->logger->warn('setting now to: '.$now.' because of X-SSC-Now hack for tests.');
        $this->now->setTimeStamp(DateTime::parse('d.m.Y', $now)->getTimestamp());
      }
    }
  }
}