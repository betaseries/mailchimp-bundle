<?php

namespace Betacie\MailChimpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Betacie\MailChimpBundle\Provider\ProviderInterface;

class SynchroniseSubscribersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Synchronizing subscribers in MailChimp')
            ->setName('betacie:mailchimp:synchronize-subscribers')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<info>%s</info>', $this->getDescription()));

        $lists = $this->getContainer()->getParameter('betacie_mailchimp.lists');
        if (sizeof($lists) == 0) {
            throw new \RuntimeException("No MailChimp list has been defined. Check the your config.yml file based on MailChimpBundle's README.md");
        }

        foreach ($lists as $listName => $listParameters) {
            $providerServiceKey = $listParameters['subscriber_provider'];

            $provider = $this->getProvider($providerServiceKey);
            $this->getContainer()->get('betacie_mailchimp.list_synchronizer')->synchronize($listName, $provider);
        }
    }

    protected function getProvider($providerServiceKey)
    {
        try {
            $provider = $this->getContainer()->get($providerServiceKey);
        } catch (ServiceNotFoundException $e) {
            throw new \InvalidArgumentException(sprintf('Provider "%s" should be defined as a service.', $providerServiceKey), $e->getCode(), $e);
        }

        if (!$provider instanceof ProviderInterface) {
            throw new \InvalidArgumentException(sprintf('Provider "%s" should implement Betacie\MailChimpBundle\Provider\ProviderInterface.', $providerServiceKey));
        }

        return $provider;
    }
}
