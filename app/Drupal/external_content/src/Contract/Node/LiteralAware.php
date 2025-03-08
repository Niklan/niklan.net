<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Node;

interface LiteralAware {

  public function setLiteral(string $literal): void;

  public function getLiteral(): string;

}
