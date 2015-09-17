<?php

namespace spec\Betacie\MailChimpBundle\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mailchimp;

class ListSynchronizerSpec extends ObjectBehavior
{
    function let(Mailchimp $mailchimp)
    {
        $this->beConstructedWith($mailchimp);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\MailChimpBundle\Subscriber\ListSynchronizer');
    }
}
