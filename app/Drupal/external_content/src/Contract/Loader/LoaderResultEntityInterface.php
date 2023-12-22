<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

/**
 * Represents loader result which loads content into Drupal entities.
 */
interface LoaderResultEntityInterface extends LoaderResultInterface {

  /**
   * Gets loaded entity type ID.
   */
  public function getEntityTypeId(): string;

  /**
   * Gets loaded entity ID.
   */
  public function getEntityId(): string;

}
