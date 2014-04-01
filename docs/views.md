#Views

## Create your own Views Fatory

```yml
  ssc.views.factory:
    class:     SSC\Views\Factory
    parent:    projectstack.views.factory
    calls:
      - [inject, ["@markdown.parser"]]
```

```php

  protected function createView($fqn, $vars = array()) {
    if ($fqn === 'SSC\Views\RegistrationTeaser') {
      return new RegistrationTeaser($this->getCountdownTeaser(), $this->getRegistrationPlacesLeft(), $vars);
    } else {
      return parent::createView($fqn, $vars);
    }
  }

  protected function injectView(\Webforge\ProjectStack\Views\View $view) {
    if ($view instanceof MarkdownTransforming) {
      $view->setMarkdownParser($this->markdownParser);
    }

    if ($view instanceof NeedsPreparing) {
      $view->prepare();
    }

    return $view;
  }
}
```