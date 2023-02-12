<?php

declare(strict_types = 1);

namespace Drupal\external_content\Annotation;

use Drupal\Component\Annotation\Plugin;
use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupInterface;

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

  /**
   * The list of file extensions which identifies this markup.
   *
   * E.g.:
   * - Plain text: 'txt'
   * - HTML: 'html', 'htm'
   * - Markdown: 'md', 'markdown'
   *
   * @var string[]
   */
  public array $markup_identifiers = [];

  /**
   * The plugin weight.
   *
   * Plugin with higher weight will be used, others will be ignored.
   */
  public int $weight = MarkupInterface::DEFAULT_WEIGHT;

}
