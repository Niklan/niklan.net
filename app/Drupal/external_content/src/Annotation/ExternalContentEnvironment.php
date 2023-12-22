<?php declare(strict_types = 1);

namespace Drupal\external_content\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Provides annotation for external content environment plugins.
 *
 * @Annotation
 */
final class ExternalContentEnvironment extends Plugin {

  /**
   * {@selfdoc}
   */
  public string $id;

  /**
   * {@selfdoc}
   */
  public string $label;

}
