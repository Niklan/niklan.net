<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Format;

use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Nodes\Node;

/**
 * @deprecated Exactly the same as HtmlElement.
 */
final class Format extends Node {

  public function __construct(
    public readonly TextFormatType $format,
    public array $attributes = [],
  ) {}

  public static function getNodeType(): string {
    return 'format';
  }

}
