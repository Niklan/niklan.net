<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\LanguageAwareStore;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;

/**
 * Defines an interface for a language-aware key/value store.
 *
 * Extends default key/value store with additional language code parameter.
 * The language is optional to make interfaces compatible, if no language
 * specified, the currently active language is used.
 *
 * @ingroup language_aware_key_value
 */
interface LanguageAwareStore extends KeyValueStoreInterface {

  /**
   * Returns the currently active language code.
   */
  public function getLanguageCode(): string;

}
