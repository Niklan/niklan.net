<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * Provides a value object for a single source inside bundle.
 */
final readonly class SourceVariation {

  /**
   * Constructs a new SourceBundleVariant instance.
   */
  public function __construct(
    public SourceInterface $source,
    public Attributes $attributes = new Attributes(),
  ) {}

}
