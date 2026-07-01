<?php declare(strict_types=1);

namespace Sofyco\Bundle\FacebookSecurityVoterBundle\DependencyInjection;

use Sofyco\Bundle\FacebookSecurityVoterBundle\Security\Voter\FacebookWebhookVoter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class FacebookSecurityVoterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $voter = new Definition(FacebookWebhookVoter::class);
        $voter->setAutowired(autowired: true);
        $voter->setAutoconfigured(autoconfigured: true);
        $voter->setArguments([
            '$secret' => $config['secret'],
            '$signature' => $config['signature'],
        ]);

        $container->setDefinition(id: FacebookWebhookVoter::class, definition: $voter);
    }
}
