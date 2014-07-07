<?php

namespace Webforge\ProjectStack\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webforge\Common\Preg;
use Webforge\Common\JS\JSONConverter;
use Webforge\ProjectStack\Test\FixturePartsManager;

class FixturePartsCommand extends Command {

    protected $fixtureParts;

    public function __construct(FixturePartsManager $fixtureParts) {
        parent::__construct();
        $this->fixtureParts = $fixtureParts;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('db:fixture-parts')
        ->setDescription('Executes arbitrary parts from fixtures from the command line.')
        ->setDefinition(array(
            new InputArgument('parts', InputArgument::REQUIRED, 'The string for all fixture parts to execute. Seperated with <dividerOption> (defaults to | )'),
            new InputOption('divider', 'd', InputOption::VALUE_REQUIRED, 'the divider used for new lines (defaults to _)', '_')
        ))
        ->setHelp(<<<EOT
Executes arbitrary parts from fixtures from the command line. For example:

    \$this->helper->fixtureParts()
        ->alice('user.funger')
        ->alice('user.pscheit')
        ->alice('user.maxmustermann')
        ->load('files', 'Uploaded')
        ->load('frontend', 'Page', array('teilnehmen'))

becomes for command line:

alice: user.funger
alice: user.pscheit
alice: user.maxmustermann
load: files Uploaded
load: frontend Page ["teilnehmen"]

you have to pass it like this:
alice: user.funger _ alice: user.pscheit _ alice: user.maxmustermann _ load: files Uploaded _ load: frontend Page ["teilnehmen"]
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lines = array_filter(
          array_map('trim', explode($input->getOption('divider'), $input->getArgument('parts'))), 
          function ($line) {
            return $line != '';
          }
        );


        foreach ($lines as $line) {
          if ($filename = Preg::qmatch($line, '/^alice\:\s*(.*?)$/', 1)) {
            $this->fixtureParts->alice($filename);
          } elseif (Preg::match($line, '/^load\:\s*([^\s]+)\s+([^\s]+)$/', $match)) {
            $this->fixtureParts->load($match[1], $match[2]);
          } elseif (Preg::match($line, '/^load\:\s*([^\s]+)\s+([^\s]+)\s+(.+)$/', $match)) {
            $this->fixtureParts->load($match[1], $match[2], JSONConverter::create()->parse($match[3]));
          } else {
            throw new \InvalidArgumentException('Cannot parse the line contents: '.$line);
          }
        }

        $this->fixtureParts
          ->execute()
          ->debug();
    }
}
