<?php

namespace Pingur\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Console\Input\InputOption;
use GuzzleHttp;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use JJG\Ping;


class ResponseCommand extends Command {

  protected $container;
  protected static $defaultName = 'response';

  public function __construct(ContainerBuilder $container) {
    parent::__construct();
    $this->container = $container;
  }

  protected function configure() {
    $HelpText = 'The <info>cert:check</info> will ping url.
<comment>Samples:</comment>
<info>pingur ping --url=foobar.com</info>';

    $this->setName("response")
      ->setDescription("check response for site")
      ->setDefinition(
        [
          new InputOption(
            'url',
            'u',
            InputOption::VALUE_OPTIONAL,
            'URL to check',
            null
          ),
          new InputOption(
            'needle',
            null,
            InputOption::VALUE_OPTIONAL,
            'Needle to check for in haystack (url)',
            null
          ),
        ]
      )
      ->setHelp($HelpText);

  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $url = $input->getOption('url');
    $needle = $input->getOption('needle');

    $client = new GuzzleHttp\Client();
    $response = $client->request('GET', "$url");
    $status_code = $response->getStatusCode();
    $content_type =$response->getHeaderLine('content-type');
    $body = $response->getBody();
    $found_output = null;
    if (isset($needle)) {
      $found_output = "needle not found";
      if ($this->NeedleInHaystack($needle, $body) == true) {
        $found_output = "needle found";
      }
    }

    $output->writeln("<info>Response:\n" .
      "\tStatus code: $status_code\n" .
      "\tContent type: $content_type\n" .
      "\tNeedle: $found_output\n" .
      "</info>");

/*

    $transport = new Swift_SmtpTransport('mailhog.wkstage.se', '1025');
    $mailer = new Swift_Mailer($transport);
    $message = new Swift_Message("$url is giving a $status_code");
    $message->setFrom('mikke.schiren@digitalistgroup.com');
    $message->setTo('mikke.schiren@digitalistgroup.com');
    $message->setBody("Hi!\nThe thing is that\n$url is giving a $status_code");
    $result = $mailer->send($message);
    */
  }


  /**
   * @param $needle
   * @param $body
   *
   * @return bool
   */
  public function NeedleInHaystack($needle, $body) {
    if (strpos($body, $needle) !== false) {
      return true;
    }
    else {
      return false;
    }
  }

}
