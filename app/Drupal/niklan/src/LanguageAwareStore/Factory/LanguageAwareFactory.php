<?php

declare(strict_types=1);

namespace Drupal\niklan\LanguageAwareStore\Factory;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareStore;

/**
 * Defines an interface for language-aware key/value stores.
 *
 * @ingroup language_aware_key_value
 */
interface LanguageAwareFactory extends KeyValueFactoryInterface {

  /**
   * Gets the key/value store for the given collection.
   *
   * @param string $collection
   *   The collection name.
   * @param string|null $language_code
   *   The language code.
   *
   * @return \Drupal\niklan\LanguageAwareStore\Repository\LanguageAwareStore
   *   The key/value store.
   */
  public function get($collection, ?string $language_code = NULL): LanguageAwareStore;

}
