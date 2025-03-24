<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

use Drupal\external_content\Contract\Node\LiteralAware;

final class TextNode extends ContentNode implements LiteralAware {

  public function __construct(
    private string $text,
  ) {
    parent::__construct('text');
  }

  #[\Override]
  public function setLiteral(string $literal): void {
    $this->text = $literal;
  }

  #[\Override]
  public function getLiteral(): string {
    return $this->text;
  }

}
