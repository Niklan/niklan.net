<?php declare(strict_types = 1);

namespace Drupal\niklan\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides an access check for GitHub signature header.
 *
 * @see https://docs.github.com/en/webhooks-and-events/webhooks/securing-your-webhooks
 *
 * @ingroup content_sync
 */
final class GitHubSignatureAccessCheck implements AccessInterface {

  /**
   * Checks access by GitHub signature header.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(Request $request): AccessResultInterface {
    $signature = $request->headers->get('X-Hub-Signature-256');

    if (!$signature) {
      return AccessResult::neutral('Expected header not found');
    }

    // Header value will be in format "sha256=token".
    [$algorithm, $token] = \explode('=', $signature);

    if ($algorithm !== 'sha256') {
      return AccessResult::neutral('Signature algorithm is invalid.');
    }

    $secret_token = Settings::get('niklan_github_secret_token');

    if (!$secret_token) {
      return AccessResult::neutral('GitHub secret token setting is not set.');
    }

    $expected_token = \hash_hmac(
      $algorithm,
      $request->getContent(),
      $secret_token,
    );

    if ($expected_token !== $token) {
      return AccessResult::forbidden("The token didn't pass validation.");
    }

    return AccessResult::allowed();
  }

}
