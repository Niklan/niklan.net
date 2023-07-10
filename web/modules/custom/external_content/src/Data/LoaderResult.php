<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Loader\LoaderResultEntityInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;

/**
 * Provides a basic loader result implementation.
 */
abstract class LoaderResult implements LoaderResultInterface {

  /**
   * Returns 'skip' result.
   */
  public static function skip(): LoaderResultInterface {
    return new LoaderResultSkip();
  }

  /**
   * Returns 'ignore' result.
   */
  public static function ignore(): LoaderResultInterface {
    return new LoaderResultIgnore();
  }

  /**
   * Returns 'entity' result.
   */
  public static function entity(string $entity_type_id, string $entity_id): LoaderResultEntityInterface {
    return new LoaderResultEntity($entity_type_id, $entity_id);
  }

}
