<?php

declare(strict_types=1);

namespace Drupal\niklan\Factory\KeyValue;

use Drupal\Component\Assertion\Inspector;
use Drupal\Core\KeyValueStore\KeyValueFactory;
use Drupal\niklan\Contract\Factory\KeyValue\LanguageAwareFactory;
use Drupal\niklan\Contract\Repository\KeyValue\LanguageAwareStore;

/**
 * @ingroup language_aware_key_value
 */
final class LanguageAwareFactoryCoordinator extends KeyValueFactory implements LanguageAwareFactory {

  const DEFAULT_SERVICE = 'keyvalue.language_aware.database';

  const SPECIFIC_PREFIX = 'keyvalue_language_aware_service_';

  const DEFAULT_SETTING = 'keyvalue_language_aware_default';

  public function get($collection): LanguageAwareStore {
    assert(Inspector::assertStringable($collection));

    return parent::get($collection);
  }

}