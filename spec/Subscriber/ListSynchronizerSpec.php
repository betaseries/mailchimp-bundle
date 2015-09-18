<?php

namespace spec\Betacie\MailchimpBundle\Subscriber;

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
        $this->shouldHaveType('Betacie\MailchimpBundle\Subscriber\ListSynchronizer');
    }
}
