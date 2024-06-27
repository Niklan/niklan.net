<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\StringContainerInterface;

/**
 * Represents a <code> element.
 */
final class Code extends Node implements StringContainerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    protected string $code,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function setLiteral(string $literal): void {
    $this->code = $literal;
  }

  /**
   * {@inheritdoc}
   */
  public function getLiteral(): string {
    return $this->code;
  }

}
