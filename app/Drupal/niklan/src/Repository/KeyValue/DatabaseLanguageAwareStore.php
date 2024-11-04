<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository\KeyValue;

use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore;

/**
 * @ingroup language_aware_key_value
 */
final class DatabaseLanguageAwareStore extends DatabaseStorage implements LanguageAwareStore {

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
    $collection,
    SerializationInterface $serializer,
    Connection $connection,
    $table = 'key_value_language_aware',
  ) {
    parent::__construct($collection, $serializer, $connection, $table);
  }

  public function has($key, ?string $language_code = NULL): bool {
    try {
      return (bool) $this
        ->connection
        ->query(
          query: 'SELECT 1 FROM {' . $this->connection->escapeTable($this->table) . '} WHERE [collection] = :collection AND [name] = :key AND [language_code] = :language_code',
          args: [
            ':collection' => $this->collection,
            ':key' => $key,
            ':language_code' => $this->resolveLanguageCode($language_code),
          ])->fetchField();
    }
    catch (\Exception $exception) {
      $this->catchException($exception);

      return FALSE;
    }
  }

  public function get($key, $default = NULL, ?string $language_code = NULL): mixed {
    $values = $this->getMultiple([$key], $language_code);

    return $values[$key] ?? $default;
  }

  public function getMultiple(array $keys, ?string $language_code = NULL): array {
    $values = [];
    try {
      $result = $this
        ->connection
        ->query(
          query: 'SELECT [name], [value] FROM {' . $this->connection->escapeTable($this->table) . '} WHERE [name] IN (:keys[]) AND [collection] = :collection AND [language_code] = :language_code',
          args: [
            ':keys[]' => $keys,
            ':collection' => $this->collection,
            ':language_code' => $this->resolveLanguageCode($language_code),
          ],
        )->fetchAllAssoc('name');
      foreach ($keys as $key) {
        if (!isset($result[$key])) {
          continue;
        }

        $values[$key] = $this->serializer->decode($result[$key]->value);
      }
    }
    catch (\Exception $exception) {
      $this->catchException($exception);

      return [];
    }

    return $values;
  }

  public function getAll(?string $language_code = NULL): array {
    try {
      $result = $this->connection->query(
          query: 'SELECT [name], [value] FROM {' . $this->connection->escapeTable($this->table) . '} WHERE [collection] = :collection AND [language_code] = :language_code',
          args: [
            ':collection' => $this->collection,
            ':language_code' => $this->resolveLanguageCode($language_code),
          ]);
    }
    catch (\Exception $e) {
      $this->catchException($e);
      $result = [];
    }

    $values = [];
    foreach ($result as $item) {
      if (!$item) {
        continue;
      }

      $values[$item->name] = $this->serializer->decode($item->value);
    }

    return $values;
  }

  public function set($key, $value, ?string $language_code = NULL): void {
    // TODO: Implement set() method.
  }

  public function setIfNotExists($key, $value, ?string $language_code = NULL): bool {
    // TODO: Implement setIfNotExists() method.
  }

  public function setMultiple(array $data, ?string $language_code = NULL): void {
    // TODO: Implement setMultiple() method.
  }

  public function rename($key, $new_key, ?string $language_code = NULL): void {
    // TODO: Implement rename() method.
  }

  public function delete($key, ?string $language_code = NULL): void {
    // TODO: Implement delete() method.
  }

  public function deleteMultiple(array $keys, ?string $language_code = NULL): void {
    // TODO: Implement deleteMultiple() method.
  }

  public static function schemaDefinition(): array {
    return [
      'description' => 'Language aware key-value storage table.',
      'fields' => [
        'collection' => [
          'description' => 'A named collection of key and value pairs.',
          'type' => 'varchar_ascii',
          'length' => 128,
          'not null' => TRUE,
          'default' => '',
        ],
        'language_code' => [
          'description' => 'The language code of the key-value pair.',
          'type' => 'varchar_ascii',
          'length' => 12,
          'not null' => TRUE,
          'default' => '',
        ],
        'name' => [
          'description' => 'The key of the key-value pair. As KEY is a SQL reserved keyword, name was chosen instead.',
          'type' => 'varchar_ascii',
          'length' => 128,
          'not null' => TRUE,
          'default' => '',
        ],
        'value' => [
          'description' => 'The value.',
          'type' => 'blob',
          'not null' => TRUE,
          'size' => 'big',
        ],
      ],
      'primary key' => ['collection', 'name', 'language_code'],
    ];
  }

  private function resolveLanguageCode(?string $language_code = NULL): string {
    if ($language_code) {
      return $language_code;
    }

    return $this->languageManager->getCurrentLanguage()->getId();
  }

}
