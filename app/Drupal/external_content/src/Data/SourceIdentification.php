<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final readonly class SourceIdentification {

  /**
   * Constructs a new IdentifiedSource instance.
   */
  public function __construct(
    public string $id,
    public Attributes $attributes = new Attributes(),
  ) {}

}
