<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Nodes\Node;

final class HtmlElement extends Node {

  public function __construct(
    public readonly string $tag,
    public readonly array $attributes = [],
  ) {}

  public static function getNodeType(): string {
    return 'html_element';
  }

}
