<?php declare(strict_types=1);

namespace Sofyco\Bundle\FacebookSecurityVoterBundle\Tests\Security\Voter;

use Sofyco\Bundle\FacebookSecurityVoterBundle\Security\Voter\FacebookWebhookVoter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class FacebookWebhookVoterTest extends KernelTestCase
{
    private const string SECRET = 'app-secret';
    private const string SIGNATURE = 'verify-token';

    private RequestStack $requestStack;
    private FacebookWebhookVoter $voter;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var RequestStack $requestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);
        $this->requestStack = $requestStack;

        /** @var FacebookWebhookVoter $voter */
        $voter = self::getContainer()->get(FacebookWebhookVoter::class);
        $this->voter = $voter;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    public function testGetVerifyTokenGranted(): void
    {
        $this->pushRequest(new Request(['hub_verify_token' => self::SIGNATURE], [], [], [], [], ['REQUEST_METHOD' => Request::METHOD_GET]));

        self::assertSame(VoterInterface::ACCESS_GRANTED, $this->vote());
    }

    public function testGetVerifyTokenDenied(): void
    {
        $this->pushRequest(new Request(['hub_verify_token' => 'wrong'], [], [], [], [], ['REQUEST_METHOD' => Request::METHOD_GET]));

        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote());
    }

    public function testPostSignatureGranted(): void
    {
        $payload = '{"object":"page","entry":[]}';
        $signature = hash_hmac('sha256', $payload, self::SECRET);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => Request::METHOD_POST, 'CONTENT_LENGTH' => (string) \strlen($payload)], $payload);
        $request->headers->set('x-hub-signature-256', 'sha256=' . $signature);

        $this->pushRequest($request);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $this->vote());
    }

    public function testPostSignatureDenied(): void
    {
        $payload = '{"object":"page","entry":[]}';

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => Request::METHOD_POST, 'CONTENT_LENGTH' => (string) \strlen($payload)], $payload);
        $request->headers->set('x-hub-signature-256', 'sha256=invalid-signature');

        $this->pushRequest($request);

        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote());
    }

    public function testPostWithoutSignatureHeaderDenied(): void
    {
        $payload = '{"object":"page","entry":[]}';

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => Request::METHOD_POST, 'CONTENT_LENGTH' => (string) \strlen($payload)], $payload);
        $this->pushRequest($request);

        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote());
    }

    public function testAbstainOnUnsupportedAttribute(): void
    {
        $this->pushRequest(new Request([], [], [], [], [], ['REQUEST_METHOD' => Request::METHOD_GET]));

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote(new NullToken(), null, ['unsupported_attribute']));
    }

    public function testDeniedWithoutRequest(): void
    {
        // request stack is empty: no current request available
        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote());
    }

    private function pushRequest(Request $request): void
    {
        $this->requestStack->push($request);
    }

    private function vote(): int
    {
        return $this->voter->vote(new NullToken(), null, [FacebookWebhookVoter::SIGNATURE]);
    }
}
