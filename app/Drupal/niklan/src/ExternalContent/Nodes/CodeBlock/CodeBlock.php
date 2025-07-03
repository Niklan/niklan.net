<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Nodes\Node;

final class CodeBlock extends Node {

  public function __construct(
    public readonly string $code,
    public readonly array $attributes = [],
  ) {}

  public static function getNodeType(): string {
    return 'niklan:code_block';
  }

}
