# mailchimp-bundle
MailChimp API Symfony Bundle

## Setup

Add bundle to your project:

```
composer require betacie/mailchimp-bundle
```

Add `Betacie\MailChimpBundle\BetacieMailChimpBundle` to your `AppKernel.php`:

``` 
    $bundles = [
        // ...
        new Betacie\MailChimpBundle\BetacieMailChimpBundle(),
    ];
```