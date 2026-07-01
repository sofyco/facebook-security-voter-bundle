# Facebook Security Voter Bundle

[![CI](https://github.com/sofyco/facebook-security-voter-bundle/actions/workflows/ci.yaml/badge.svg)](https://github.com/sofyco/facebook-security-voter-bundle/actions/workflows/ci.yaml)
[![codecov](https://codecov.io/gh/sofyco/facebook-security-voter-bundle/branch/main/graph/badge.svg)](https://codecov.io/gh/sofyco/facebook-security-voter-bundle)
[![stable](http://poser.pugx.org/sofyco/facebook-security-voter-bundle/v)](https://packagist.org/packages/sofyco/facebook-security-voter-bundle)

## Installation

```bash
composer req sofyco/facebook-security-voter-bundle
```

## Configuration

The bundle exposes a `facebook_security_voter` configuration node with two
required, non-empty string arguments:

- `secret` — Facebook App Secret; verifies the `x-hub-signature-256`
  HMAC-SHA256 header on `POST` webhooks.
- `signature` — verify token; verifies the `hub_verify_token` query parameter
  on `GET` verification requests.

```yaml
# config/packages/facebook_security_voter.yaml
facebook_security_voter:
    secret: '%env(FACEBOOK_APP_SECRET)%'
    signature: '%env(FACEBOOK_VERIFY_TOKEN)%'
```

Both options are resolved into the `FacebookWebhookVoter` constructor
(`$secret` and `$signature`); the `RequestStack` argument is autowired.

## Usage

The bundle registers a security voter (`FacebookWebhookVoter`) that grants
access when the current request is a legitimate Facebook webhook call:

- `GET` requests are validated against the `hub_verify_token` query parameter;
- `POST` requests are validated against the `x-hub-signature-256` HMAC-SHA256 header.

Use the `FacebookWebhookVoter::SIGNATURE` attribute to protect a controller:

```php
use Sofyco\Bundle\FacebookSecurityVoterBundle\Security\Voter\FacebookWebhookVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(FacebookWebhookVoter::SIGNATURE)]
public function __invoke(): Response
{
    // ...
}
```
