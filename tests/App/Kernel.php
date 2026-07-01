<?php declare(strict_types=1);

namespace Sofyco\Bundle\FacebookSecurityVoterBundle\Tests\App;

use Sofyco\Bundle\FacebookSecurityVoterBundle\FacebookSecurityVoterBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SecurityBundle();
        yield new FacebookSecurityVoterBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => '098f6bcd4621d373cade4e832627b4f6',
        ]);

        $container->extension('security', [
            'providers' => [
                'facebook_users' => ['memory' => null],
            ],
            'firewalls' => [
                'main' => ['pattern' => '^/'],
            ],
        ]);

        $container->extension('facebook_security_voter', [
            'secret' => 'app-secret',
            'signature' => 'verify-token',
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
    }
}
