<?php

declare(strict_types=1);

namespace Drupal\niklan\LanguageAwareStore\Repository;

use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\DatabaseException;
use Drupal\Core\Database\Query\Merge;

/**
 * Provides the database language-aware key-value store.
 *
 * @ingroup language_aware_key_value
 */
final readonly class DatabaseLanguageAwareStore implements LanguageAwareStore {

  public function __construct(
    private string $languageCode,
    private string $collection,
    private SerializationInterface $serializer,
    private Connection $connection,
    private string $table,
  ) {}

  #[\Override]
  public function has($key): bool {
    try {
      return (bool) $this
        ->connection
        ->select($this->table)
        ->fields($this->table, ['name'])
        ->condition('collection', $this->collection)
        ->condition('name', $key)
        ->condition('language_code', $this->languageCode)
        ->range(0, 1)
        ->execute()
        ->fetchField();
    }
    catch (\Exception $exception) {
      $this->catchException($exception);

      return FALSE;
    }
  }

  #[\Override]
  public function get($key, $default = NULL): mixed {
    $values = $this->getMultiple([$key]);

    return $values[$key] ?? $default;
  }

  #[\Override]
  public function getMultiple(array $keys): array {
    $values = [];
    try {
      $result = $this
        ->connection
        ->select($this->table)
        ->fields($this->table, ['name', 'value'])
        ->condition('collection', $this->collection)
        ->condition('language_code', $this->languageCode)
        ->condition('name', $keys, 'IN')
        ->execute()
        ->fetchAllAssoc('name');

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

  #[\Override]
  public function getAll(): array {
    try {
      $result = $this
        ->connection
        ->select($this->table)
        ->fields($this->table, ['name', 'value'])
        ->condition('collection', $this->collection)
        ->condition('language_code', $this->languageCode)
        ->execute();
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

  #[\Override]
  public function set($key, $value): void {
    try {
      $this->doSet($key, $value);
    }
    catch (\Exception $e) {
      // If there was an exception, try to create the table.
      if (!$this->ensureTableExists()) {
        throw $e;
      }

      $this->doSet($key, $value);
    }
  }

  #[\Override]
  public function setIfNotExists($key, $value): bool {
    try {
      return $this->doSetIfNotExists($key, $value);
    }
    catch (\Exception $exception) {
      // If there was an exception, try to create the table.
      if ($this->ensureTableExists()) {
        return $this->doSetIfNotExists($key, $value);
      }

      throw $exception;
    }
  }

  #[\Override]
  public function setMultiple(array $data): void {
    foreach ($data as $key => $value) {
      $this->set($key, $value);
    }
  }

  #[\Override]
  public function rename($key, $new_key): void {
    try {
      $this
        ->connection
        ->update($this->table)
        ->fields(['name' => $new_key])
        ->condition('collection', $this->collection)
        ->condition('language_code', $this->languageCode)
        ->condition('name', $key)
        ->execute();
    }
    catch (\Exception $e) {
      $this->catchException($e);
    }
  }

  #[\Override]
  public function delete($key): void {
    $this->deleteMultiple([$key]);
  }

  #[\Override]
  public function deleteMultiple(array $keys): void {
    while ($keys) {
      try {
        $this
          ->connection
          ->delete($this->table)
          ->condition('collection', $this->collection)
          ->condition('language_code', $this->languageCode)
          ->condition('name', \array_splice($keys, 0, 1000), 'IN')
          ->execute();
      }
      catch (\Exception $exception) {
        $this->catchException($exception);
      }
    }
  }

  #[\Override]
  public function getLanguageCode(): string {
    return $this->languageCode;
  }

  #[\Override]
  public function getCollectionName(): string {
    return $this->collection;
  }

  #[\Override]
  public function deleteAll(): void {
    try {
      $this
        ->connection
        ->delete($this->table)
        ->condition('collection', $this->collection)
        ->execute();
    }
    catch (\Exception $e) {
      $this->catchException($e);
    }
  }

  public static function schemaDefinition(): array {
    return [
      'description' => 'Language-aware key-value storage table.',
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

  protected function doSetIfNotExists($key, $value): bool {
    $result = $this
      ->connection
      ->merge($this->table)
      ->insertFields([
        'collection' => $this->collection,
        'language_code' => $this->languageCode,
        'name' => $key,
        'value' => $this->serializer->encode($value),
      ])
      ->condition('collection', $this->collection)
      ->condition('name', $key)
      ->execute();

    return $result === Merge::STATUS_INSERT;
  }

  protected function doSet($key, $value): void {
    $this
      ->connection
      ->merge($this->table)
      ->keys([
        'collection' => $this->collection,
        'language_code' => $this->languageCode,
        'name' => $key,
      ])
      ->fields(['value' => $this->serializer->encode($value)])
      ->execute();
  }

  /**
   * Check if the table exists and create it if not.
   *
   * @return bool
   *   TRUE if the table exists, FALSE if it does not exists.
   */
  protected function ensureTableExists(): bool {
    try {
      $database_schema = $this->connection->schema();
      $database_schema->createTable($this->table, $this->schemaDefinition());
    }
    // If the table already exists, then attempting to recreate it will throw an
    // exception. In this case just catch the exception and do nothing.
    catch (DatabaseException) {
    }
    catch (\Exception) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Act on an exception when the table might not have been created.
   *
   * If the table does not yet exist, that's fine, but if the table exists and
   * yet the query failed, then the exception needs to propagate if it is not
   * a DatabaseException. Due to race conditions it is possible that another
   * request has created the table in the meantime. Therefore we can not rethrow
   * for any database exception.
   *
   * @param \Exception $e
   *   The exception.
   *
   * @throws \Exception
   */
  protected function catchException(\Exception $e): void {
    if (!($e instanceof DatabaseException) && $this->connection->schema()->tableExists($this->table)) {
      throw $e;
    }
  }

}
