<?php

namespace Betacie\MailchimpBundle\Subscriber;

use Mailchimp;
use Betacie\MailchimpBundle\Provider\ProviderInterface;

class ListSynchronizer
{
    protected $mailchimp;

    public function __construct(Mailchimp $mailchimp)
    {
        $this->mailchimp = $mailchimp;
    }

    public function synchronize($listName, ProviderInterface $provider)
    {
        $listData = $this->mailchimp->lists->getList([
            'list_name' => $listName
        ]);

        if ($listData['total'] === 0) {
            throw new \RuntimeException(sprintf('The list "%s" was not found in Mailchimp. You need to create it first in Mailchimp backend.', $listName));
        }

        $listId = $listData['data'][0]['id'];

        $subscribers = array_map(function(Subscriber $subscriber) {
            return [
                'email' => ['email' => $subscriber->getEmail()]
            ];
        }, $provider->getSubscribers());

        $this->mailchimp->lists->batchSubscribe($listId, $subscribers, false);
    }
}
