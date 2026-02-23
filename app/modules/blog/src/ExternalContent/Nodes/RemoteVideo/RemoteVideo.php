<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Nodes\Node;

final class RemoteVideo extends Node {

  public function __construct(
    public readonly string $url,
  ) {}

  public static function getNodeType(): string {
    return 'niklan:remote_video';
  }

}
