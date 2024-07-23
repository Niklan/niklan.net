<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\StringContainerInterface;

/**
 * Represents a simple plain text inside elements.
 */
final class PlainText extends Node implements StringContainerInterface {

  /**
   * Constructs a new PlainText object.
   *
   * @param string $text
   *   The text content.
   */
  public function __construct(
    protected string $text,
  ) {}

  #[\Override]
  public function setLiteral(string $literal): void {
    $this->text = $literal;
  }

  #[\Override]
  public function getLiteral(): string {
    return $this->text;
  }

}
