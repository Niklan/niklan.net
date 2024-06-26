<?php declare(strict_types = 1);

namespace Drupal\niklan\Node\ExternalContent;

use Drupal\external_content\Node\Node;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class Video extends Node {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly string $src,
    public readonly string $title,
  ) {}

}
