<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository\KeyValue;

use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\Merge;
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
    try {
      $this->doSet($key, $value, $language_code);
    }
    catch (\Exception $e) {
      // If there was an exception, try to create the table.
      if (!$this->ensureTableExists()) {
        throw $e;
      }

      $this->doSet($key, $value, $language_code);
    }
  }

  public function doSetIfNotExists($key, $value, ?string $language_code = NULL): bool {
    $result = $this
      ->connection
      ->merge($this->table)
      ->insertFields([
        'collection' => $this->collection,
        'language_code' => $this->resolveLanguageCode($language_code),
        'name' => $key,
        'value' => $this->serializer->encode($value),
      ])
      ->condition('collection', $this->collection)
      ->condition('name', $key)
      ->execute();

    return $result === Merge::STATUS_INSERT;
  }

  public function setIfNotExists($key, $value, ?string $language_code = NULL): bool {
    try {
      return $this->doSetIfNotExists($key, $value, $language_code);
    }
    catch (\Exception $exception) {
      // If there was an exception, try to create the table.
      if ($this->ensureTableExists()) {
        return $this->doSetIfNotExists($key, $value, $language_code);
      }

      throw $exception;
    }
  }

  public function setMultiple(array $data, ?string $language_code = NULL): void {
    foreach ($data as $key => $value) {
      $this->set($key, $value, $language_code);
    }
  }

  public function rename($key, $new_key, ?string $language_code = NULL): void {
    try {
      $this
        ->connection
        ->update($this->table)
        ->fields(['name' => $new_key])
        ->condition('collection', $this->collection)
        ->condition('language_code', $this->resolveLanguageCode($language_code))
        ->condition('name', $key)
        ->execute();
    }
    catch (\Exception $e) {
      $this->catchException($e);
    }
  }

  public function delete($key, ?string $language_code = NULL): void {
    $this->deleteMultiple([$key], $language_code);
  }

  public function deleteMultiple(array $keys, ?string $language_code = NULL): void {
    while ($keys) {
      try {
        $this
          ->connection
          ->delete($this->table)
          ->condition('collection', $this->collection)
          ->condition('language_code', $this->resolveLanguageCode($language_code))
          ->condition('name', \array_splice($keys, 0, 1000), 'IN')
          ->execute();
      }
      catch (\Exception $exception) {
        $this->catchException($exception);
      }
    }
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

  protected function doSet($key, $value, ?string $language_code = NULL): void {
    $this
      ->connection
      ->merge($this->table)
      ->keys([
        'collection' => $this->collection,
        'language_code' => $this->resolveLanguageCode($language_code),
        'name' => $key,
      ])
      ->fields(['value' => $this->serializer->encode($value)])
      ->execute();
  }

  private function resolveLanguageCode(?string $language_code = NULL): string {
    if ($language_code) {
      return $language_code;
    }

    return $this->languageManager->getCurrentLanguage()->getId();
  }

}
