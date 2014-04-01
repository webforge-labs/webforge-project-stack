# Testing

to use the container in the tests (and / or the kernel) extend: `Webforge\ProjectStack\Test\Base`

## Kernel TestCase

to use a kernel testcase (like the one in symfony) extend: `Webforge\ProjectStack\Test\KernelTestCase`

### examples

test json responses

```php

  public function testRetrievingAllNewsAsJSON() {
    $this->jsonRequest('GET', '/cms/news-entries');

    $this->assertJsonResponse()
      //->debug()
      ->property('news-entries')->isArray()->length($this->greaterThan(0))
        ->key(0)
          ->property('id')->end()
          ->property('published')->end()
        ->end()
      ->end();
  }
```

test html responses

```php

  public function testNewFormGeneration() {
    $crawler = $this->client->request('GET', '/cms/news-entries/new');

    $this->assertSymfonyResponse($this->client->getResponse())->code(200);

    $this->css('form', $crawler->html())->count(1)->asContext()
      ->css('div.panel[role=teaser]')->count(1)
        ->css('input.form-control')->atLeast(1)->hasAttribute('data-bind')->end()
      ->end()

      ->css('div.panel[role=page-content]')->count(1)
        ->css('input.form-control')->atLeast(1)->hasAttribute('data-bind')->end()
      ->end()
    ;
  }
```