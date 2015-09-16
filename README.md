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