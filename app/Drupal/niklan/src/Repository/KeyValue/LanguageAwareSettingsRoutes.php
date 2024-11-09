<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository\KeyValue;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @ingroup language_aware_key_value
 */
final readonly class LanguageAwareSettingsRoutes implements EventSubscriberInterface {

  public const array ROUTES_TO_ENHANCE = [
    'niklan.about.settings',
    'niklan.contact.settings',
    'niklan.services.settings',
    'niklan.support.settings',
  ];

  public function __construct(
    private LanguageManagerInterface $languageManager,
  ) {}

  public function onAlterRoutes(RouteBuildEvent $event): void {
    $collection = $event->getRouteCollection();

    foreach (self::ROUTES_TO_ENHANCE as $route_name) {
      $route = $collection->get($route_name);

      if (!$route) {
        continue;
      }

      $this->enhanceRoute($route_name, $route, $collection);
    }
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    $events[RoutingEvents::ALTER] = 'onAlterRoutes';

    return $events;
  }

  private function enhanceRoute(string $route_name, Route $base_route, RouteCollection $collection): void {
    $base_route->setDefault(
      name: 'key_value_language_aware_code',
      default: $this->languageManager->getDefaultLanguage()->getId(),
    );

    $additional_languages = [];

    foreach ($this->languageManager->getLanguages() as $language) {
      if ($language->isDefault()) {
        continue;
      }

      $additional_languages[] = $language->getId();
    }

    $route = clone $base_route;
    $route->setPath("{$base_route->getPath()}/{key_value_language_aware_code}");
    $route->setRequirement(
      key: 'key_value_language_aware_code',
      regex: '^(' . \implode('|', $additional_languages) . ')$',
    );
    $collection->add("{$route_name}.translate.language", $route);
  }

}
