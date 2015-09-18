<?php

namespace spec\Betacie\MailchimpBundle\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('charles@terrasse.fr');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\MailchimpBundle\Subscriber\Subscriber');
    }

    function it_has_an_email()
    {
        $this->getEmail()->shouldReturn('charles@terrasse.fr');
    }

    function it_can_have_a_firstname()
    {
        $this->setFirstname('Charles')->shouldReturn($this);
        $this->getFirstname()->shouldReturn('Charles');
    }

    function it_can_have_a_lastname()
    {
        $this->setLastname('Terrasse')->shouldReturn($this);
        $this->getLastname()->shouldReturn('Terrasse');
    }
}
