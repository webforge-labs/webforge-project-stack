<?php

namespace Webforge\ProjectStack\Symfony\Console;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;
use Webforge\Common\System\System;

class CompileModelCommand extends \Webforge\Console\Command\CommandAdapter {

  public function __construct(System $system) {
    parent::__construct('project:compile-model', $system);
  }

  public function doExecute(CommandInput $input, CommandOutput $output, CommandInteraction $interact, System $system) {
    if ($system->passthru('webforge-doctrine-compiler orm:compile --extension=Serializer etc/doctrine/model.json src/php/') === 0) {
      return $system->passthru('bin'.DIRECTORY_SEPARATOR.'cli doctrine:schema:update --force --dump-sql');
    }

    return 1;
  }
}
