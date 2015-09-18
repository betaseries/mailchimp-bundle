<?php

namespace Betacie\MailchimpBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Betacie\MailchimpBundle\DependencyInjection\BetacieMailchimpExtension;

class BetacieMailchimpBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new BetacieMailchimpExtension();
    }
}