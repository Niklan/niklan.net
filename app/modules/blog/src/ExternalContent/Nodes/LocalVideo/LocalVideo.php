<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\LocalVideo;

use Drupal\external_content\Nodes\Node;

final class LocalVideo extends Node {

  public function __construct(
    public readonly string $src,
    public readonly string $title,
    public readonly array $attributes = [],
  ) {}

  public static function getNodeType(): string {
    return 'niklan:local_video';
  }

}
