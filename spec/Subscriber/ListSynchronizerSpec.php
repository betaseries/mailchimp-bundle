<?php

namespace spec\Betacie\MailchimpBundle\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Betacie\MailchimpBundle\Subscriber\ListRepository;

class ListSynchronizerSpec extends ObjectBehavior
{
    function let(ListRepository $listRepository, LoggerInterface $logger)
    {
        $this->beConstructedWith($listRepository, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\MailchimpBundle\Subscriber\ListSynchronizer');
    }
}
