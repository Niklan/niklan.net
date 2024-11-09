<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Menu;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Repository\KeyValue\LanguageAwareSettingsRoutes;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ingroup language_aware_key_value
 */
final class KeyValueLanguageAwareLocalTask extends DeriverBase implements ContainerDeriverInterface {

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container, $base_plugin_id): self {
    return new self(
      $container->get(LanguageManagerInterface::class),
    );
  }

  #[\Override]
  public function getDerivativeDefinitions($base_plugin_definition): array {
    foreach (LanguageAwareSettingsRoutes::ROUTES_TO_ENHANCE as $route_name) {
      // @todo This tab should be managed by the consumer.
      $this->derivatives[$route_name] = [
        'title' => new TranslatableMarkup('Settings'),
        'route_name' => $route_name,
        'base_route' => $route_name,
      ] + $base_plugin_definition;

      foreach ($this->languageManager->getLanguages() as $language) {
        $language_task = "key_value_language_aware:$route_name:{$language->getId()}";
        $this->derivatives[$language_task] = [
          'route_name' => $language->isDefault() ? $route_name : "$route_name.translate.language",
          'route_parameters' => [
            'key_value_language_aware_code' => $language->getId(),
          ],
          'parent_id' => "{$base_plugin_definition['id']}:$route_name",
          'title' => $language->getName(),
          'weight' => $language->getWeight(),
        ] + $base_plugin_definition;
      }
    }

    return $this->derivatives;
  }

}
