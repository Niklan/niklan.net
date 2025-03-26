<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

use Drupal\external_content\Contract\DataStructure\Node\LiteralAware;

final class CodeNode extends ElementNode implements LiteralAware {

  public function __construct(
    private string $code,
  ) {
    parent::__construct();
  }

  #[\Override]
  public function setLiteral(string $literal): void {
    $this->code = $literal;
  }

  #[\Override]
  public function getLiteral(): string {
    return $this->code;
  }

  public static function getType(): string {
    return 'code';
  }

}
