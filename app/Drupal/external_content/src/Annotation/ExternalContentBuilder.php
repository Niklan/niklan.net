<?php declare(strict_types = 1);

namespace Drupal\external_content\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines annotation for builder plugins.
 *
 * @Annotation
 */
final class ExternalContentBuilder extends Plugin {

  /**
   * The format ID.
   */
  public string $id;

  /**
   * The format label.
   */
  public string $label;

  /**
   * The plugin weight.
   */
  public int $weight = 0;

}
