<?php

declare(strict_types=1);

namespace Drupal\niklan\Factory\KeyValue;

use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\niklan\Contract\Factory\KeyValue\LanguageAwareFactory;
use Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore;
use Drupal\niklan\Repository\KeyValue\DatabaseLanguageAwareStore;

/**
 * @ingroup language_aware_key_value
 */
final class DatabaseLanguageAwareFactory implements LanguageAwareFactory {

  /**
   * @var \Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore[]
   */
  private array $storages = [];

  public function __construct(
    private readonly SerializationInterface $serializer,
    private readonly Connection $connection,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  #[\Override]
  public function get(string $collection): LanguageAwareStore {
    if ($this->storages[$collection] ?? FALSE) {
      return $this->storages[$collection];
    }

    return $this->storages[$collection] = new DatabaseLanguageAwareStore(
      languageManager: $this->languageManager,
      collection: $collection,
      serializer: $this->serializer,
      connection: $this->connection,
    );
  }

}
