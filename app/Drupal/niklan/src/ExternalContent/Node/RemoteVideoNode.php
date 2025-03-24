<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Node;

use Drupal\external_content\DataStructure\Nodes\ContentNode;

final class RemoteVideoNode extends ContentNode {

  public function __construct(
    public string $videoUrl,
  ) {
    parent::__construct('niklan:remote_video');
  }

}
