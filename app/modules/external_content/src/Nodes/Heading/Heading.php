<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Nodes\Node;

/**
 * @deprecated Exactly the same as HtmlElement.
 */
final class Heading extends Node {

  public function __construct(
    public readonly HeadingTagType $tag,
  ) {}

  public static function getNodeType(): string {
    return 'heading';
  }

}
