<?php

namespace Betacie\MailChimpBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Betacie\MailChimpBundle\DependencyInjection\BetacieMailChimpExtension;

class BetacieMailChimpBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new BetacieMailChimpExtension();
    }
}