<?php

declare(strict_types=1);

namespace Drupal\niklan\Contract\Repository\KeyValue;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;

/**
 * @ingroup language_aware_key_value
 */
interface LanguageAwareStore extends KeyValueStoreInterface {

  public function has($key, ?string $language_code = NULL): bool;

  public function get($key, $default = NULL, ?string $language_code = NULL): mixed;

  public function getMultiple(array $keys, ?string $language_code = NULL): array;

  public function getAll(?string $language_code = NULL): array;

  public function set($key, $value, ?string $language_code = NULL): void;

  public function setIfNotExists($key, $value, ?string $language_code = NULL): bool;

  public function setMultiple(array $data, ?string $language_code = NULL): void;

  public function rename($key, $new_key, ?string $language_code = NULL): void;

  public function delete($key, ?string $language_code = NULL): void;

  public function deleteMultiple(array $keys, ?string $language_code = NULL): void;

}
