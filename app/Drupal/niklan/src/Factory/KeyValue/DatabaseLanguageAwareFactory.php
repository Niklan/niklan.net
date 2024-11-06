<?php

declare(strict_types=1);

namespace Drupal\niklan\Factory\KeyValue;

use Drupal\Component\Assertion\Inspector;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\niklan\Contract\Factory\KeyValue\LanguageAwareFactory;
use Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore;
use Drupal\niklan\Repository\KeyValue\DatabaseLanguageAwareStore;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Provides a factory for database key/value store.
 *
 * @ingroup language_aware_key_value
 */
final class DatabaseLanguageAwareFactory implements LanguageAwareFactory {

  /**
   * The list of initialized storages.
   *
   * @var array<string, array<\Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore>>
   */
  private array $storages = [];

  public function __construct(
    #[Autowire(service: 'serialization.phpserialize')]
    private readonly SerializationInterface $serializer,
    private readonly Connection $connection,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function get($collection, ?string $language_code = NULL): LanguageAwareStore {
    \assert(Inspector::assertStringable($collection));
    $language_code ??= $this->languageManager->getCurrentLanguage()->getId();

    if ($this->storages[$collection][$language_code] ?? FALSE) {
      return $this->storages[$collection][$language_code];
    }

    return $this->storages[$collection][$language_code] = new DatabaseLanguageAwareStore(
      languageCode: $language_code,
      collection: $collection,
      serializer: $this->serializer,
      connection: $this->connection,
      table: 'key_value_language_aware',
    );
  }

}