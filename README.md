# mailchimp-bundle

This bundle will help you synchronise your project's users into MailChimp. New users will be added to MailChimp, existing users will be updated and user no longer in your project will be deleted of MailChimp.

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
            # mc_language: 'fr'
            subscriber_providers: 'yourapp.provider1'
        list2:
            subscriber_providers: 'yourapp.provider2'
```

Where `listX` is the name of your MailChimp lists, and `yourapp.providerX` is the key of your provider's service that will provide the subscribers that need to be synchronized in MailChimp. The key `mc_language` is optional and will set this language for all subscribers in this list, see [the list of accepted language codes](http://kb.mailchimp.com/lists/managing-subscribers/view-and-edit-subscriber-languages#code).

### Example of a provider

Your provider should be accessible via a service key (the same you reference in `subscriber_providers` in the configuration above):

```yaml
services:
    yourapp_mailchimp_subscriber_provider:
        class: YourApp\App\Newsletter\SubscriberProvider
        arguments: [@yourapp_user_repository]
```

It should implement `Betacie\MailChimpBundle\Provider\ProviderInterface` and return an array of `Betacie\MailChimpBundle\Subscriber\Subscriber` objects. The first argument of the `Subscriber` object is its e-mail, the second argument is an array of merge tags values you need to add in MailChimp's backend in your list settings under `List fields and *|MERGE|* tags`.

```php
<?php

namespace YourApp\App\Newsletter;

use Betacie\MailchimpBundle\Provider\ProviderInterface;
use Betacie\MailchimpBundle\Subscriber\Subscriber;
use YourApp\Model\User\UserRepository;
use YourApp\Model\User\User;

class SubscriberProvider implements ProviderInterface
{
    // these tags should match the one you added in MailChimp's backend
    const TAG_NICKNAME =           'NICKNAME';
    const TAG_GENDER =             'GENDER';
    const TAG_BIRTHDATE =          'BIRTHDATE';
    const TAG_LAST_ACTIVITY_DATE = 'LASTACTIVI';
    const TAG_REGISTRATION_DATE =  'REGISTRATI';
    const TAG_CITY =               'CITY';

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getSubscribers()
    {
        $users = $this->userRepository->findSubscribers();

        $subscribers = array_map(function(User $user) {
            $subscriber = new Subscriber($user->getEmail(), [
                self::TAG_NICKNAME => $user->getNickname(),
                self::TAG_GENDER => $user->getGender(),
                self::TAG_BIRTHDATE => $user->getBirthdate() ? $user->getBirthdate()->format('Y-m-d') : null,
                self::TAG_CITY => $user->getCity(),
                self::TAG_LAST_ACTIVITY_DATE => $user->getLastActivityDate() ? $user->getLastActivityDate()->format('Y-m-d') : null,
                self::TAG_REGISTRATION_DATE => $user->getRegistrationDate() ? $user->getRegistrationDate()->format('Y-m-d') : null,
            ]);

            return $subscriber;
        }, $users);

        return $subscribers;
    }
}
```

### Synchronizing subscibers

You can then synchronize all subscribers by calling the symfony command `app/console betacie:mailchimp:synchronize-subscribers`.