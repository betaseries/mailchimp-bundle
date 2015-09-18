<?php

namespace spec\Betacie\MailchimpBundle\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mailchimp;
use Psr\Log\LoggerInterface;

class ListSynchronizerSpec extends ObjectBehavior
{
    function let(Mailchimp $mailchimp, LoggerInterface $logger)
    {
        $this->beConstructedWith($mailchimp, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\MailchimpBundle\Subscriber\ListSynchronizer');
    }
}
