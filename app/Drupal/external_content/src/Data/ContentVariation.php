<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Node\Content;

/**
 * {@selfdoc}
 */
final readonly class ContentVariation {

  /**
   * Constructs a new ContentVariation instance.
   */
  public function __construct(
    public Content $content,
    public Attributes $attributes = new Attributes(),
  ) {}

}
