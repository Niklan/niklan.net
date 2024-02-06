<?php declare(strict_types = 1);

namespace Drupal\niklan\Node;

use Drupal\external_content\Node\Node;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class DrupalMediaElement extends Node {

  /**
   * Constructs a new DrupalMediaElement instance.
   */
  public function __construct(
    public readonly string $uuid,
    public readonly ?string $alt = NULL,
  ) {}

}
