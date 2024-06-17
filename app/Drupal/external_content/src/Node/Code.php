<?php declare(strict_types = 1);

namespace Drupal\external_content\Node;

/**
 * Represents a <code> element.
 */
final class Code extends Node {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly string $code,
  ) {}

}
