<?php declare(strict_types=1);

namespace Sofyco\Bundle\FacebookSecurityVoterBundle\Security\Voter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class FacebookWebhookVoter extends Voter
{
    public const string SIGNATURE = 'facebook_webhook_signature';

    public function __construct(private readonly RequestStack $request, private readonly string $secret, private readonly string $signature)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::SIGNATURE;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if (null === $request = $this->request->getCurrentRequest()) {
            return false;
        }

        if (Request::METHOD_GET === $request->getMethod()) {
            return $request->query->getString('hub_verify_token') === $this->signature;
        }

        $requestSignature = str_replace('sha256=', '', $request->headers->get('x-hub-signature-256') ?? '');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $this->secret);

        return hash_equals($expectedSignature, $requestSignature);
    }
}
