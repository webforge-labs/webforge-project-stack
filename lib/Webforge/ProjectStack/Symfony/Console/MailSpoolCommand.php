<?php

namespace Webforge\ProjectStack\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class MailSpoolCommand extends Command {

  protected $spoolPath;

  public function __construct($spoolPath) {
    parent::__construct();
    $this->spoolPath = $spoolPath;

  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('mail:spool')
      ->setDescription('Returns all mails currently spooled.')
      ->setDefinition(array(
        new InputOption(
          'clear', null, InputOption::VALUE_NONE,
          'Clears the whole spool (!).'
        )
      ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $finder = Finder::create()->files()->in($this->spoolPath);

    if ($input->getOption('clear')) {
      foreach($finder as $file) {
        unlink($file);
      }

      return 0;
    }

    $getters = array(
      'subject'=>'getSubject',
      'returnPath'=>'getReturnPath',
      'sender'=>'getSender',
      'from'=>'getFrom',
      'replyTo'=>'getReplyTo',
      'to'=>'getTo',
      'cc'=>'getCc',
      'bcc'=>'getBcc',
      'body'=>'getBody'
    );

    $result = array();
    foreach ($finder as $mailFile) {
      /** @var $message \Swift_Message */
      $message = unserialize(file_get_contents($mailFile));

      $export = new \stdClass;

      foreach ($getters as $var => $getter) {
        $export->$var = $message->$getter();
      }

      $export->headers = array();
      foreach ($message->getHeaders()->getAll() as $header) {
        $export->headers[$header->getFieldName()] = $header->getFieldBody();
      }

      if ($export->headers['X-Swift-To']) {
        $export->to = $export->headers['X-Swift-To'];
      }

      $result[] = $export;
    }

    print json_encode($result, JSON_PRETTY_PRINT);
  }
}
