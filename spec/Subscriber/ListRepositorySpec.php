<?php

namespace spec\Betacie\MailchimpBundle\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mailchimp;
use Mailchimp_Lists;
use Psr\Log\LoggerInterface;

class ListRepositorySpec extends ObjectBehavior
{
    function let(Mailchimp $mailchimp, LoggerInterface $logger, Mailchimp_Lists $mailchimpLists)
    {
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
