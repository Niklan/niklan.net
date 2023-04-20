<?php declare(strict_types = 1);

namespace Drupal\external_content\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines external content source annotation.
 *
 * @Annotation
 */
final class ExternalContentSource extends Plugin {

  /**
   * The plugin ID.
   */
  protected string $id;

}
