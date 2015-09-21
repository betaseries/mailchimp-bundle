<?php

namespace Betacie\MailchimpBundle\Subscriber;

use Mailchimp;
use Psr\Log\LoggerInterface;
use Betacie\MailchimpBundle\Provider\ProviderInterface;

class ListSynchronizer
{
    protected $mailchimp;
    protected $logger;

    public function __construct(Mailchimp $mailchimp, LoggerInterface $logger)
    {
        $this->mailchimp = $mailchimp;
        $this->logger = $logger;
    }

    public function synchronize($listName, ProviderInterface $provider)
    {
        $listId = $this->getListId($listName);

        $subscribers = $provider->getSubscribers();

        $this->batchSubscribe($listId, $subscribers);
        $this->unsubscribeDifference($listId, $subscribers);
    }

    protected function batchSubscribe($listId, array $subscribers = [])
    {
        $subscribers = array_map(function(Subscriber $subscriber) {
            return [
                'email' => ['email' => $subscriber->getEmail()],
                'merge_vars' => $subscriber->getMergeTags()
            ];
        }, $subscribers);

        $result = $this->mailchimp->lists->batchSubscribe(
            $listId,
            $subscribers,
            false, // do not use dual optin (to prevent sending another confirmation e-mail)
            true // do update the subscriber if it already exists
        );

        if ($result['add_count'] > 0) {
            $this->logger->info(sprintf('%s subscribers were added.', $result['add_count']));
        }

        if ($result['update_count'] > 0) {
            $this->logger->info(sprintf('%s subscribers were updated.', $result['update_count']));
        }

        if ($result['error_count'] > 0) {
            $this->logger->error(sprintf('%s subscribers errored.', $result['error_count']));
            foreach ($result['errors'] as $error) {
                $this->logger->error(sprintf('Subscriber "%s" has not been processed: "%s"', $error['email']['email'], $error['error']));
            }
        }
    }

    protected function unsubscribeDifference($listId, $subscribers)
    {
        $mailchimpSubscribers = $this->mailchimp->lists->members($listId);
        $mailchimpEmails = array_map(function($data) { return $data['email']; }, $mailchimpSubscribers['data']);

        $internalEmails = array_map(function(Subscriber $subscriber) { return $subscriber->getEmail(); }, $subscribers);

        // emails that are present in mailchimp but not internally should be unsubscribed
        $diffenceEmails = array_diff($mailchimpEmails, $internalEmails);
        if (sizeof($diffenceEmails) == 0) {
            return;
        }

        $diffenceEmails = array_map(function($email) {
            return [
                'email' => $email,
            ];
        }, $diffenceEmails);

        $result = $this->mailchimp->lists->batchUnsubscribe(
            $listId,
            $diffenceEmails,
            true, // and remove it from the list
            false // do not send goodbye email
        );

        if ($result['success_count'] > 0) {
            $this->logger->info(sprintf('%s emails were unsubscribed from mailchimp.', $result['success_count']));
        }

        if ($result['error_count'] > 0) {
            $this->logger->error(sprintf('%s subscribers failed to be unsubscribed.', $result['error_count']));
            foreach ($result['errors'] as $error) {
                $this->logger->error(sprintf('Subscriber "%s" has not been unsubscribed: "%s"', $error['email']['email'], $error['error']));
            }
        }
    }

    protected function getListId($listName)
    {
        $listData = $this->mailchimp->lists->getList([
            'list_name' => $listName
        ]);

        if (
            !isset($listData['total']) || 
            $listData['total'] === 0
        ) {
            throw new \RuntimeException(sprintf('The list "%s" was not found in Mailchimp. You need to create it first in Mailchimp backend.', $listName));
        }

        if (!isset($listData['data'][0]['id'])) {
            throw new \RuntimeException('List id could not be found.');
        }

        return $listData['data'][0]['id'];
    }
}
