<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * {@selfdoc}
 */
final readonly class IdentifiedSource {

  /**
   * Constructs a new IdentifiedSource instance.
   */
  public function __construct(
    public string $id,
    public SourceInterface $source,
    public Attributes $attributes = new Attributes(),
  ) {}

}
