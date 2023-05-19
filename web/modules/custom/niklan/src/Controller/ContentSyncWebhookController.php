<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a content sync webhook controller.
 *
 * @ingroup content_sync
 */
final class ContentSyncWebhookController {

  /**
   * Handles webhook content sync trigger.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   */
  public function handle(): JsonResponse {
    // @todo Add event and trigger it here.
    return new JsonResponse(['message' => 'ok']);
  }

}
