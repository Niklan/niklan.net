<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Nodes\Node;

final class Heading extends Node {

  public function __construct(
    public readonly HeadingTagType $tagType,
  ) {}

  public static function getNodeType(): string {
    return 'heading';
  }

}
