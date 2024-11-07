<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository\KeyValue;

use Drupal\Component\Assertion\Inspector;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareFactory;
use Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a factory based on the service container.
 *
 * @ingroup language_aware_key_value
 */
final class ServiceContainerLanguageAwareFactory implements LanguageAwareFactory {

  /**
   * The service ID of default implementation.
   */
  private const string DEFAULT_SERVICE = 'keyvalue.language_aware.database';

  /**
   * The default settings allow users to override services.
   */
  private const string DEFAULT_SETTING = 'keyvalue_language_aware_default';

  /**
   * The list of initialized storages.
   *
   * @var \Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore
   */
  private array $stores = [];

  public function __construct(
    private readonly ContainerInterface $container,
    private readonly LanguageManagerInterface $languageManager,
    #[Autowire(param: 'factory.keyvalue')]
    private readonly array $options = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  public function get($collection, ?string $language_code = NULL): LanguageAwareStore {
    \assert(Inspector::assertStringable($collection));
    $language_code ??= $this->languageManager->getCurrentLanguage()->getId();

    if (!isset($this->stores[$collection][$language_code])) {
      if (isset($this->options[$collection][$language_code])) {
        $service_id = $this->options[$collection][$language_code];
      }
      elseif (isset($this->options[self::DEFAULT_SETTING][$language_code])) {
        $service_id = $this->options[self::DEFAULT_SETTING][$language_code];
      }
      else {
        $service_id = self::DEFAULT_SERVICE;
      }

      $this->stores[$collection][$language_code] = $this
        ->container
        ->get($service_id)
        ->get($collection, $language_code);
    }

    return $this->stores[$collection][$language_code];
  }

}
