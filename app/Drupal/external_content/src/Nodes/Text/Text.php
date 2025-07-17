<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Nodes\Node;

final class Text extends Node implements \Stringable {

  public function __construct(
    public readonly string $text,
  ) {}

  public static function getNodeType(): string {
    return 'text';
  }

  public function __toString(): string {
    return $this->text;
  }

}
