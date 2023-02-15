<?php declare(strict_types = 1);

namespace Drupal\external_content\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines annotation for loader plugins.
 *
 * @Annotation
 */
final class ExternalContentLoader extends Plugin {

  /**
   * The loader ID.
   */
  public string $id;

  /**
   * The loader label.
   */
  public string $label;

}
