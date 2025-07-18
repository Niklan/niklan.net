<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Image;

use Drupal\external_content\Nodes\Node;

final class Image extends Node {

  public function __construct(
    public readonly string $src,
    public readonly string $alt,
  ) {}

  public static function getNodeType(): string {
    return 'niklan:image';
  }

}
