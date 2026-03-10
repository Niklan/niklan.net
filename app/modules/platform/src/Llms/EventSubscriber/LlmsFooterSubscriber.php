<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms\EventSubscriber;

use Drupal\app_platform\Llms\LlmsResponseAlterEvent;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class LlmsFooterSubscriber implements EventSubscriberInterface {

  public function __construct(
    private TranslationInterface $translation,
  ) {}

  public function onAlter(LlmsResponseAlterEvent $event): void {
    $lines = [
      $this->buildCanonicalLink($event),
      $this->buildIndexLink(),
    ];

    $event->append("\n\n---\n\n" . \implode("\n", $lines));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsResponseAlterEvent::class => ['onAlter', -100]];
  }

  private function buildCanonicalLink(LlmsResponseAlterEvent $event): string {
    // Build the canonical (human-readable) URL for this page.
    // We use getPathInfo() for the path because it contains the path alias.
    // Url::fromRoute() with 'path_processing' => FALSE returns the system
    // path (/node/123) instead of the alias.
    // For query parameters we read the original QUERY_STRING from the server
    // because $request->query has already been modified by inbound path
    // processors (e.g., PagerPathProcessor converts page=3 to page=2).
    $url = $event->request->getPathInfo();

    $queryString = $event->request->server->get('QUERY_STRING', '');
    \assert(\is_string($queryString));
    \parse_str($queryString, $query);
    unset($query['_format']);

    if ($query !== []) {
      $url .= '?' . \http_build_query($query);
    }

    $label = (string) $this->translation->translate('View page');

    return \sprintf('- [%s](%s)', $label, $url);
  }

  private function buildIndexLink(): string {
    $label = (string) $this->translation->translate('Site index');

    return \sprintf('- [%s](/llms.txt)', $label);
  }

}
