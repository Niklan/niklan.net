<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\MediaReference;

use Drupal\external_content\Nodes\Node;

final class MediaReference extends Node {

  public function __construct(
    public readonly string $uuid,
    public readonly array $metadata = [],
  ) {}

  public static function getNodeType(): string {
    return 'niklan:media_reference';
  }

}
