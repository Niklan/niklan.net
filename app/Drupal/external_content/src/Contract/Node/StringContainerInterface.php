<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Node;

/**
 * Represents a node which directly contains line(s) of text.
 */
interface StringContainerInterface {

  public function setLiteral(string $literal): void;

  public function getLiteral(): string;

}
