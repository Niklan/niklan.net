<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Nodes\ContentNode;

final class RemoteVideoNode extends ContentNode {

  public function __construct(
    public string $videoUrl,
  ) {}

  public static function getType(): string {
    return 'niklan:remote_video';
  }

}
