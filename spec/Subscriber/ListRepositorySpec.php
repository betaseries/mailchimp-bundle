<?php

namespace spec\Betacie\MailchimpBundle\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mailchimp;
use Mailchimp_Lists;
use Psr\Log\LoggerInterface;
use Betacie\MailchimpBundle\Subscriber\Subscriber;

class ListRepositorySpec extends ObjectBehavior
{
    function let(Mailchimp $mailchimp, LoggerInterface $logger, Mailchimp_Lists $mailchimpLists, Subscriber $subscriber)
    {
        $this->prepareSubscriber($subscriber);
        $this->prepareMailchimpLists($mailchimpLists);

        $mailchimp->lists = $mailchimpLists;

        $this->beConstructedWith($mailchimp, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Betacie\MailchimpBundle\Subscriber\ListRepository');
    }

    function it_can_find_a_list_by_its_name($mailchimpLists)
    {
        $this->findByName('toto')->shouldReturn(['id' => 123]);
        $this
            ->shouldThrow(new \RuntimeException('The list "tutu" was not found in Mailchimp. You need to create it first in Mailchimp backend.'))
            ->duringFindByName('tutu')
        ;
    }

    function it_subscribe_a_subscriber($subscriber, $mailchimpLists)
    {
        $mailchimpLists->subscribe(
            1337,
            ['email' => 'charles@terrasse.fr'],
            ['FIRSTNAME' => 'Charles'],
            'html',
            false,
            true
        )->shouldBeCalled();

        $this->subscribe(1337, $subscriber);
    }

    function it_unsubscribe_a_subscriber($subscriber, $mailchimpLists)
    {
        $mailchimpLists->unsubscribe(
            1337,
            ['email' => 'charles@terrasse.fr'],
            true,
            false,
            false
        )->shouldBeCalled();

        $this->unsubscribe(1337, $subscriber);
    }

    protected function prepareSubscriber(Subscriber $subscriber)
    {
        $subscriber->getEmail()->willReturn('charles@terrasse.fr');
        $subscriber->getMergeTags()->willReturn(['FIRSTNAME' => 'Charles']);
    }

    protected function prepareMailchimpLists(Mailchimp_Lists $mailchimpLists)
    {
        $mailchimpLists->getList(['list_name' => 'toto'])->willReturn([
            'total' => 1, 
            'data' => [
                ['id' => 123]
            ]
        ]);

        $mailchimpLists->getList(['list_name' => 'tutu'])->willReturn([
            'total' => 0
        ]);
    }
}
