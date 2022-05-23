<?php

declare(strict_types=1);

namespace Drupal\external_content\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines external content markup annotation.
 *
 * @Annotation
 */
final class ExternalContentMarkup extends Plugin {

  /**
   * The format ID.
   */
  public string $id;

  /**
   * The format label.
   */
  public string $label;

}
