<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Drupal\taxonomy\TermInterface;

/**
 * Defines an interface for tag controller.
 */
interface TagControllerInterface {

  /**
   * Builds page with all tags.
   *
   * @return array
   *   An array with page content.
   */
  public function collection(): array;

  /**
   * Builds a single tag page.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   The tag entity.
   *
   * @return array
   *   An array with page contents.
   */
  public function page(TermInterface $term): array;

}
