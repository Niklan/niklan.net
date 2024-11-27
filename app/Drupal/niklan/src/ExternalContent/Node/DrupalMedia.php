<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Node;

use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Node;

/**
 * @ingroup content_sync
 */
final class DrupalMedia extends Node {

  public function __construct(
    public readonly string $type,
    public readonly string $uuid,
    public readonly Data $data = new Data(),
  ) {}

}
