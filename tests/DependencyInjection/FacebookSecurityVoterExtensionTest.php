<?php declare(strict_types=1);

namespace Sofyco\Bundle\FacebookSecurityVoterBundle\Tests\DependencyInjection;

use Sofyco\Bundle\FacebookSecurityVoterBundle\Security\Voter\FacebookWebhookVoter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FacebookSecurityVoterExtensionTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    public function testServiceExists(): void
    {
        self::assertTrue(self::getContainer()->has(FacebookWebhookVoter::class));
    }
}
