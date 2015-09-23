<?php

namespace Betacie\MailchimpBundle\Event;

use Betacie\MailchimpBundle\Subscriber\ListRepository;
use Betacie\MailchimpBundle\Event\SubscriberEvent;

class SubscriberListener
{
    protected $listRepository;

    public function __construct(ListRepository $listRepository)
    {
        $this->listRepository = $listRepository;
    }

    public function onSubscribe(SubscriberEvent $event)
    {
        $this->listRepository->subscribe(
            $this->getListId($event->getListName()),
            $event->getSubscriber()
        );
    }

    public function onUnsubscribe(SubscriberEvent $event)
    {
        $this->listRepository->unsubscribe(
            $this->getListId($event->getListName()),
            $event->getSubscriber()
        );
    }

    protected function getListId($listName)
    {
        $listData = $this->listRepository->findByName($listName);
        
        return $listData['id'];
    }
}
