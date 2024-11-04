<?php

declare(strict_types=1);

namespace Drupal\niklan\Contract\Factory\KeyValue;

use Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore;

/**
 * @ingroup language_aware_key_value
 */
interface LanguageAwareFactory {

  public function get(string $collection): LanguageAwareStore;

}
