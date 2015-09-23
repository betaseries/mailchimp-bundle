<?php

namespace spec\Betacie\MailchimpBundle\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Betacie\MailchimpBundle\Subscriber\Subscriber;

class SubscriberEventSpec extends ObjectBehavior
{
    function let(Subscriber $subscriber)
    {
        $this->beConstructedWith('listname', $subscriber);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\MailchimpBundle\Event\SubscriberEvent');
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
    }

    function it_has_a_listname()
    {
        $this->getListName()->shouldReturn('listname');
    }

    function it_has_a_subscriber($subscriber)
    {
        $this->getSubscriber()->shouldReturn($subscriber);
    }
}
