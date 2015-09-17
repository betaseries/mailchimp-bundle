# mailchimp-bundle
MailChimp API Symfony Bundle

## Setup

Add bundle to your project:

```bash
composer require betacie/mailchimp-bundle
```

Add `Betacie\MailChimpBundle\BetacieMailChimpBundle` to your `AppKernel.php`:

```php 
$bundles = [
    // ...
        new Betacie\MailChimpBundle\BetacieMailChimpBundle(),
];
```

## Usage

You need to add the list in MailChimp's backend first.

For each list you must define a configuration in your `config.yml`:

```yaml
betacie_mailchimp:
    api_key: YOURMAILCHIMPAPIKEY
    lists:
        list1:
            subscriber_providers: 'yourapp.provider1'
        list2:
            subscriber_providers: 'yourapp.provider2'
```

Where `listX` is the name of your MailChimp lists, and `yourapp.providerX` is the key of your provider's service that will provide the subscribers that need to be synchronized in MailChimp.

### Example of a provider

Your provider should be accessible via a service key (the same you reference in `subscriber_providers` in the configuration above):

```yaml
services:
    diwi_mailchimp_subscriber_provider:
        class: Diwi\App\Newsletter\SubscriberProvider
        arguments: [@diwi_user_repository]
```

It should implement `Betacie\MailChimpBundle\Provider\ProviderInterface` and return an array of `Betacie\MailChimpBundle\Subscriber\Subscriber` objects. 

```php
<?php

namespace Diwi\App\Newsletter;

use Betacie\MailChimpBundle\Provider\ProviderInterface;
use Betacie\MailChimpBundle\Subscriber\Subscriber;
use Diwi\Model\User\UserRepository;
use Diwi\Model\User\User;

class SubscriberProvider implements ProviderInterface
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getSubscribers()
    {
        $users = $this->userRepository->findSubscribers();

        $subscribers = array_map(function(User $user) {
            $subscriber = new Subscriber($user->getEmail());
            $subscriber
                ->setFirstname($user->getFirstname())
                ->getLastname($user->getLastname())
            ;

            return $subscriber;
        }, $users);

        return $subscribers;
    }
}
```

The subscriber array will then be validated so be sure to format it accordingly.

### Subscriber format

A subscriber should be returned as a formatted array:

```php
$subscriber = [
    'email' => 'foo@bar.com',
    'fistname' => 'Charles',
    'lastname' => 'Terrasse'
];
```