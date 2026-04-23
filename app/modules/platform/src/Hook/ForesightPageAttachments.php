<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\SessionConfigurationInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpFoundation\RequestStack;

#[Hook('page_attachments')]
final readonly class ForesightPageAttachments {

  public function __construct(
    private SessionConfigurationInterface $sessionConfiguration,
    private RequestStack $requestStack,
  ) {}

  public function __invoke(array &$attachments): void {
    if (!Settings::get('app_foresight', TRUE)) {
      return;
    }

    $attachments['#cache']['contexts'][] = 'session.exists';

    $request = $this->requestStack->getCurrentRequest();
    if ($request && $this->sessionConfiguration->hasSession($request)) {
      return;
    }

    $attachments['#attached']['library'][] = 'app_platform/foresightjs.init';
  }

}
