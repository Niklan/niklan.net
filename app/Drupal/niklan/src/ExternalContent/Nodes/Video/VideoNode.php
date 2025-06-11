<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Video;

use Drupal\external_content\Nodes\ContentNode;

final class VideoNode extends ContentNode {

  public function __construct(
    public string $src,
    public string $title,
  ) {
    parent::__construct();
  }

  public static function getType(): string {
    return 'niklan:video';
  }

}
